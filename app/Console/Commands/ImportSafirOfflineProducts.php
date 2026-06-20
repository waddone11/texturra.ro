<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\SafirExcel;
use App\Models\Product;
use App\Models\ProductVariation;

class ImportSafirOfflineProducts extends Command
{
    protected $signature = 'import:safir-offline-products {--limit=10} {--test}';
    protected $description = 'Import SafirExcel products without links (no scraping or images)';

    public function handle()
    {
        $query = SafirExcel::where('safir_link_exist', false)
            ->where('safir_parsed', 0);

        if ($this->option('test')) {
            $query->limit(1);
        } else {
            $query->limit((int) $this->option('limit'));
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info("No offline SafirExcel records to process.");
            return 0;
        }

        foreach ($records as $safir) {
            $this->info("Processing offline SafirExcel ID: {$safir->safir_excel_id}");

            $title = $safir->safir_excel_name ?: 'Unknown Product';
            $slug = Str::slug($title);
            $buc_set = $safir->safir_buc_set ?: ($safir->safir_buc_bax ?: ($safir->safir_set_bax ?: 1));
            $price = $safir->safir_sell_price / $buc_set;

            $product = new Product();
            $product->name              = $title;
            $product->slug              = $slug;
            $product->description       = '<p>Offline product without scraped description.</p>';
            $product->description_plain = 'Offline product without scraped description.';
            $product->brand_name        = 'safir';
            $product->safir_excel_id    = $safir->safir_excel_id;
            $product->safir_excel_name  = $safir->safir_excel_name;
            $product->safir_excel_link  = null;
            $product->category_id       = 164; // Unknown category
            $product->vat_id            = 1;
            $product->images            = ['']; // No image
            $product->price             = $price;
            $product->buc_set           = $safir->safir_buc_set;
            $product->set_bax           = $safir->safir_set_bax;
            $product->buc_bax           = $safir->safir_buc_bax;
            $product->currency          = 'RON';
            $product->commission_percentage = 0;
            $product->general_stock     = 0;
            $product->status            = 1;
            $product->source_link       = null;
            $product->save();

            $product->product_code = 'TEX-' . $product->id;
            $product->save();

            $variation = new ProductVariation();
            $variation->product_id = $product->id;
            $variation->sku        = 'TEX-SKU-' . $product->id;
            $variation->price      = 0;
            $variation->stock      = 9999;
            $variation->save();

            $safir->safir_parsed = 1;
            $safir->save();

            $this->info("✅ Imported offline product: {$title}");
        }

        $this->info("Finished importing offline Safir products.");
        return 0;
    }
}
