<?php

namespace App\Console\Commands;

use App\Models\Color;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Classifies duplicate color names in the palette into:
 *   A — exact duplicates (same name + hex + group): pure seed dups, safe to collapse.
 *   B — same name, different hex: real distinct hues, need RENAME (user decides).
 *   C — same name + hex, different group: ambiguous, user decides.
 *
 * DRY-RUN by default. --execute performs ONLY Category A collapses (the safe,
 * zero-FK-impact deletes). B/C require explicit naming decisions first.
 */
class DedupPalette extends Command
{
    protected $signature = 'palette:dedup {--execute : Perform Category A collapses (default is DRY-RUN, no writes)}';

    protected $description = 'Classify + dedup duplicate palette colors (dry-run by default)';

    public function handle(): int
    {
        $execute = (bool) $this->option('execute');
        $this->line($execute
            ? '<bg=red;fg=white> EXECUTE — will DELETE Category A exact-duplicates </>'
            : '<bg=blue;fg=white> DRY-RUN — no writes </>');

        $norm = fn ($s) => mb_strtolower(trim((string) $s));

        // duplicate normalized names
        $dupNames = Color::selectRaw('LOWER(TRIM(name)) n, COUNT(*) c')
            ->groupBy('n')->havingRaw('c > 1')->pluck('n');

        $this->line("Distinct duplicate names: {$dupNames->count()}");
        $this->newLine();

        $catA = [];   // collapse actions: ['name','hex','group','keep','delete'=>[]]
        $catB = [];   // ['name' => [ ['repr_id','hex','group','count','proposed'] ... ]]
        $catC = [];   // ['name','hex','reprs'=>[ ['id','group','group_id'] ]]

        foreach ($dupNames as $n) {
            $rows = Color::whereRaw('LOWER(TRIM(name)) = ?', [$n])
                ->with('group')->orderBy('id')->get();

            // subgroups by (hex, group_id)
            $subs = [];
            foreach ($rows as $r) {
                $key = $norm($r->cod_css) . '|' . $r->color_group_id;
                $subs[$key][] = $r;
            }

            // exact-dup collapses within each (hex,group) subgroup
            $reprs = [];
            foreach ($subs as $key => $group) {
                usort($group, fn ($a, $b) => $a->id <=> $b->id);
                $keep = $group[0];
                $reprs[] = $keep;
                if (count($group) > 1) {
                    $catA[] = [
                        'name'   => $keep->name,
                        'hex'    => $keep->cod_css,
                        'group'  => $keep->group->name ?? '—',
                        'keep'   => $keep->id,
                        'delete' => array_map(fn ($x) => $x->id, array_slice($group, 1)),
                    ];
                }
            }

            // after collapsing exact dups, is the name still ambiguous?
            if (count($reprs) > 1) {
                $distinctHexes = collect($reprs)->map(fn ($r) => $norm($r->cod_css))->unique();
                if ($distinctHexes->count() > 1) {
                    // Category B — distinct hues sharing a name
                    $variants = [];
                    foreach ($reprs as $i => $r) {
                        $variants[] = [
                            'repr_id'  => $r->id,
                            'hex'      => $r->cod_css,
                            'group'    => $r->group->name ?? '—',
                            // keep the first (lowest id) as-is, propose a disambiguator for the rest
                            'proposed' => $i === 0 ? $r->name . ' (păstrează)' : $r->name . ' (' . $r->cod_css . ')',
                        ];
                    }
                    $catB[$rows[0]->name] = $variants;
                } else {
                    // Category C — same hex, different group
                    $catC[] = [
                        'name'  => $rows[0]->name,
                        'hex'   => $reprs[0]->cod_css,
                        'reprs' => array_map(fn ($r) => [
                            'id' => $r->id, 'group' => $r->group->name ?? '—', 'group_id' => $r->color_group_id,
                        ], $reprs),
                    ];
                }
            }
        }

        // ---------- CATEGORY A ----------
        $aDeleteCount = collect($catA)->sum(fn ($a) => count($a['delete']));
        $this->info("CATEGORY A — exact duplicates (safe collapse): {$aDeleteCount} rows to delete across " . count($catA) . ' name/hex/group sets');
        $this->table(
            ['name', 'hex', 'group', 'KEEP id', 'DELETE ids'],
            collect($catA)->map(fn ($a) => [
                mb_strimwidth($a['name'], 0, 26, '…'), $a['hex'], mb_strimwidth($a['group'], 0, 20, '…'),
                $a['keep'], implode(',', $a['delete']),
            ])->all()
        );

        // ---------- CATEGORY B ----------
        $this->newLine();
        $this->warn('CATEGORY B — same name, DIFFERENT hex (need RENAME — your decision): ' . count($catB) . ' names');
        foreach ($catB as $name => $variants) {
            $this->line("  \"{$name}\":");
            foreach ($variants as $v) {
                $this->line("     id={$v['repr_id']} hex={$v['hex']} group={$v['group']}  → propun: \"{$v['proposed']}\"");
            }
        }

        // ---------- CATEGORY C ----------
        $this->newLine();
        $this->warn('CATEGORY C — same name+hex, DIFFERENT group (your decision): ' . count($catC) . ' names');
        foreach ($catC as $c) {
            $groups = collect($c['reprs'])->map(fn ($r) => "{$r['group']} (id={$r['id']})")->implode(' | ');
            $this->line("  \"{$c['name']}\" hex={$c['hex']}: {$groups}");
        }

        // ---------- SUMMARY ----------
        $this->newLine();
        $this->info('SUMMARY');
        $this->line('  Category A (collapse): ' . count($catA) . " sets, {$aDeleteCount} deletions");
        $this->line('  Category B (rename):   ' . count($catB) . ' names');
        $this->line('  Category C (decide):   ' . count($catC) . ' names');
        $this->line('  keep policy (A): lowest id (first seeded)');

        if (! $execute) {
            $this->newLine();
            $this->comment('DRY-RUN complete. Nothing written. --execute performs ONLY Category A collapses (B/C need your naming decisions first).');
            return self::SUCCESS;
        }

        // ---------- EXECUTE: Category A only ----------
        $deleted = 0;
        DB::transaction(function () use ($catA, &$deleted) {
            foreach ($catA as $a) {
                $deleted += Color::whereIn('id', $a['delete'])->delete();
            }
        });
        $this->info("Deleted {$deleted} exact-duplicate colors (Category A). B/C untouched.");

        return self::SUCCESS;
    }
}
