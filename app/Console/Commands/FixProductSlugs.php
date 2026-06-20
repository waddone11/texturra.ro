<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

class FixProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:product-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure unique slugs and product codes for all products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating product slugs and product codes...');

        $products = Product::all();
        $updatedCount = 0;

        foreach ($products as $product) {
            $originalSlug = $product->slug;
            $originalCode = $product->product_code;

            // Convert name to lowercase
            $slugBase = Str::lower($product->name);

            // Extract age range inside parentheses (e.g., "(size 7-10)")
            preg_match('/\(([^)]+)\)/', $slugBase, $matches);
            $ageRange = isset($matches[1]) ? $matches[1] : '';

            // Remove parentheses and content inside from the main slug
            $slugBase = preg_replace('/\([^)]+\)/', '', $slugBase);

            // Replace all non-alphanumeric characters with "-"
            $slugBase = preg_replace('/[^a-z0-9]+/i', '-', $slugBase);

            // Remove multiple dashes
            $slugBase = preg_replace('/-+/', '-', $slugBase);

            // Trim dashes from start and end
            $slugBase = trim($slugBase, '-');

            // Convert "size X-Y" to "X-Y-ani"
            if (!empty($ageRange)) {
                $ageRange = preg_replace('/[^0-9-]+/', '', $ageRange); // Keep only numbers and dashes
                if (!empty($ageRange)) {
                    $slugBase .= "-{$ageRange}-ani";
                }
            }

            // Final slug with product ID
            $newSlug = "{$slugBase}-id-{$product->id}";

            // Generate unique product code
            $newProductCode = "AR-{$product->id}";

            // Check if updates are needed
            if ($product->slug !== $newSlug || $product->product_code !== $newProductCode) {
                $product->update([
                    'slug' => $newSlug,
                    'product_code' => $newProductCode
                ]);
                $updatedCount++;
                $this->info("Updated: ID {$product->id} | Slug: {$originalSlug} → {$newSlug} | Code: {$originalCode} → {$newProductCode}");
            }
        }

        $this->info("✅ {$updatedCount} products updated successfully.");
    }
}
