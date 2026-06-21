<?php

namespace App\Console\Commands;

use App\Models\Material;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Maps the legacy "Material" attribute_values (attached to product variations)
 * into the clean product_material pivot. DRY-RUN by default — writes only with --execute.
 * Mirrors products:migrate-colors.
 */
class MigrateProductMaterials extends Command
{
    protected $signature = 'products:migrate-materials {--execute : Actually write product_material (default is DRY-RUN)}';

    protected $description = 'Map legacy material variations into the product_material pivot (dry-run by default)';

    public function handle(): int
    {
        $execute = (bool) $this->option('execute');
        $this->line($execute
            ? '<bg=red;fg=white> EXECUTE — will WRITE product_material </>'
            : '<bg=blue;fg=white> DRY-RUN — no writes </>');

        $materialAttrId = DB::table('attributes')->where('name', 'Material')->value('id');
        if (! $materialAttrId) {
            $this->error('No "Material" attribute found — aborting.');
            return self::FAILURE;
        }
        $this->line("Material attribute id = {$materialAttrId}");

        // material value name (normalized) → materials.id
        $materialsByName = Material::all()->keyBy(fn (Material $m) => mb_strtolower(trim($m->name)));

        $links = DB::table('product_variation_attribute_values as piv')
            ->join('attribute_values as av', 'piv.attribute_value_id', '=', 'av.id')
            ->join('product_variations as pv', 'piv.product_variation_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('av.attribute_id', $materialAttrId)
            ->select('pv.product_id', 'p.name as product_name', 'av.value as material_name')
            ->get();

        $this->line("Material-variation links found: {$links->count()}");
        $this->newLine();

        $mapped = [];   // keyed by product-material to honour the unique constraint
        $unmapped = [];

        foreach ($links as $link) {
            $key = mb_strtolower(trim($link->material_name));
            $material = $materialsByName->get($key);

            if (! $material) {
                $unmapped[] = $link;
                continue;
            }

            $mapKey = $link->product_id . '-' . $material->id;
            $mapped[$mapKey] = [
                'product_id' => $link->product_id,
                'product_name' => $link->product_name,
                'material_id' => $material->id,
                'material_name' => $material->name,
            ];
        }

        $this->info('MAPPED cleanly: ' . count($mapped) . ' product_material rows');
        $this->table(
            ['product_id', 'product', 'material_id', 'material'],
            collect($mapped)->map(fn ($r) => [
                $r['product_id'], mb_strimwidth($r['product_name'], 0, 36, '…'), $r['material_id'], $r['material_name'],
            ])->values()->all()
        );

        $this->newLine();
        $this->warn('UNMAPPED (no material match): ' . count($unmapped));
        foreach ($unmapped as $u) {
            $this->line("  product_id={$u->product_id} material=\"{$u->material_name}\"");
        }

        // products with variations but no material after migration
        $mappedProductIds = collect($mapped)->pluck('product_id')->unique();
        $productsWithVariations = DB::table('product_variations')->distinct()->pluck('product_id')->unique();
        $noMaterial = $productsWithVariations->diff($mappedProductIds);
        $this->newLine();
        $this->warn('Products WITH variations but NO material after migration: ' . $noMaterial->count());
        if ($noMaterial->isNotEmpty()) {
            foreach (DB::table('products')->whereIn('id', $noMaterial)->pluck('name', 'id') as $id => $name) {
                $this->line("  product_id={$id} \"{$name}\"");
            }
        }

        $this->newLine();
        $this->info('SUMMARY: mapped=' . count($mapped) . ' unmapped=' . count($unmapped)
            . ' distinct-products-with-material=' . $mappedProductIds->count());

        if (! $execute) {
            $this->newLine();
            $this->comment('DRY-RUN complete. Nothing written. Re-run with --execute after confirmation.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($mapped) {
            foreach ($mapped as $r) {
                DB::table('product_material')->updateOrInsert(
                    ['product_id' => $r['product_id'], 'material_id' => $r['material_id']],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        });
        $this->info('WROTE ' . count($mapped) . ' rows to product_material.');

        return self::SUCCESS;
    }
}
