<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\Color;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Maps legacy color attribute_values (attached to product variations) into the
 * new product_color pivot. DRY-RUN by default — writes ONLY with --execute.
 *
 * Stock source (proposed): the color-variation's own `stock`. Reported next to
 * the product's general_stock so the artificial split is visible before any write.
 */
class MigrateProductColors extends Command
{
    protected $signature = 'products:migrate-colors {--execute : Actually write to product_color (default is DRY-RUN, no writes)}';

    protected $description = 'Map legacy color variations into the product_color pivot (dry-run by default)';

    public function handle(): int
    {
        $execute = (bool) $this->option('execute');
        $this->line($execute
            ? '<bg=red;fg=white> EXECUTE — will WRITE to product_color </>'
            : '<bg=blue;fg=white> DRY-RUN — no writes </>');

        $culoareId = Attribute::where('name', 'Culoare')->value('id');
        if (! $culoareId) {
            $this->error('No "Culoare" attribute found — aborting.');
            return self::FAILURE;
        }
        $this->line("Culoare attribute id = {$culoareId}");

        // Each color-variation link: variation -> attribute_value (of Culoare).
        $links = DB::table('product_variation_attribute_values as piv')
            ->join('attribute_values as av', 'piv.attribute_value_id', '=', 'av.id')
            ->join('product_variations as pv', 'piv.product_variation_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('av.attribute_id', $culoareId)
            ->select('pv.id as variation_id', 'pv.product_id', 'pv.stock as variation_stock',
                'p.general_stock', 'p.name as product_name', 'av.value as color_name')
            ->get();

        $this->line("Color-variation links found: {$links->count()}");
        $this->newLine();

        $mapped = [];      // keyed by product_id-color_id to honour the unique constraint
        $unmapped = [];
        $dupes = [];

        foreach ($links as $link) {
            $matches = Color::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($link->color_name))])->get();

            if ($matches->isEmpty()) {
                $unmapped[] = ['reason' => 'NO Color match', 'link' => $link];
                continue;
            }
            if ($matches->count() > 1) {
                $unmapped[] = ['reason' => 'AMBIGUOUS (' . $matches->count() . ' colors named the same)', 'link' => $link];
                continue;
            }

            $color = $matches->first();
            $key = $link->product_id . '-' . $color->id;
            $row = [
                'product_id'    => $link->product_id,
                'product_name'  => $link->product_name,
                'color_id'      => $color->id,
                'color_name'    => $color->name,
                'stock'         => (int) $link->variation_stock,
                'general_stock' => (int) $link->general_stock,
            ];

            if (isset($mapped[$key])) {
                $dupes[] = $row; // same product+color twice — unique constraint would collapse
            } else {
                $mapped[$key] = $row;
            }
        }

        // ---- MAPPED ----
        $this->info('MAPPED cleanly: ' . count($mapped) . ' product_color rows');
        $this->table(
            ['product_id', 'product', 'color_id', 'color', 'stock (src: variation)', 'product.general_stock'],
            collect($mapped)->map(fn ($r) => [
                $r['product_id'],
                mb_strimwidth($r['product_name'], 0, 32, '…'),
                $r['color_id'],
                $r['color_name'],
                $r['stock'],
                $r['general_stock'],
            ])->values()->all()
        );

        // ---- UNMAPPED ----
        $this->newLine();
        $this->warn('UNMAPPED: ' . count($unmapped));
        foreach ($unmapped as $u) {
            $this->line("  [{$u['reason']}] product_id={$u['link']->product_id} color_name=\"{$u['link']->color_name}\" (variation {$u['link']->variation_id})");
        }

        // ---- DUPLICATES (same product+color) ----
        if ($dupes) {
            $this->newLine();
            $this->warn('DUPLICATE product+color (unique constraint would keep one): ' . count($dupes));
            foreach ($dupes as $d) {
                $this->line("  product_id={$d['product_id']} color={$d['color_name']} (id {$d['color_id']})");
            }
        }

        // ---- PRODUCTS THAT WOULD END WITH NO COLOR ----
        $mappedProductIds = collect($mapped)->pluck('product_id')->unique();
        $productsWithVariations = DB::table('product_variations')->distinct()->pluck('product_id')->unique();
        $noColor = $productsWithVariations->diff($mappedProductIds);
        $this->newLine();
        $this->warn('Products WITH variations but NO color after migration: ' . $noColor->count());
        if ($noColor->isNotEmpty()) {
            $names = DB::table('products')->whereIn('id', $noColor)->pluck('name', 'id');
            foreach ($names as $id => $name) {
                $this->line("  product_id={$id} \"{$name}\"");
            }
        }

        // ---- SUMMARY ----
        $this->newLine();
        $this->info('SUMMARY');
        $this->line("  mapped rows: " . count($mapped));
        $this->line("  unmapped:    " . count($unmapped));
        $this->line("  duplicates:  " . count($dupes));
        $this->line("  stock source: variation.stock (per-color variation's own stock)");

        if (! $execute) {
            $this->newLine();
            $this->comment('DRY-RUN complete. No data written. Re-run with --execute to write (after confirmation).');
            return self::SUCCESS;
        }

        // ---- EXECUTE (only with --execute) ----
        // Decision: stock = 0 on every row (legacy variation.stock is an
        // artificial general_stock/2 split — junk seed data). Real per-color
        // stock is entered in Filament afterwards.
        DB::transaction(function () use ($mapped) {
            foreach ($mapped as $r) {
                DB::table('product_color')->updateOrInsert(
                    ['product_id' => $r['product_id'], 'color_id' => $r['color_id']],
                    ['stock' => 0, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        });
        $this->info('WROTE ' . count($mapped) . ' rows to product_color (stock=0).');

        return self::SUCCESS;
    }
}
