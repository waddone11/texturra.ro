<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateProductCategories extends Command
{
    protected $signature = 'products:update-categories';
    protected $description = 'Update product categories based on keywords in the title';

    public function handle()
    {
        // Define keyword-category mapping
        $categories = [
            'TRENING' => 30,
            'JOGGER' => 26,
            'PANTALON' => 26,
            'HANORAC' => 28,
            'BLUZA' => 32,
            'ROCHIE' => 34,
            'SET' => 30,
            'COLANT' => 35,
            'TRICOU' => 24,
        ];

        // Fetch all products
        $products = Product::all();
        $updatedCount = 0;

        foreach ($products as $product) {
            $title = strtoupper($product->name); // Convert to uppercase for case-insensitive matching

            foreach ($categories as $keyword => $categoryId) {
                if (str_contains($title, $keyword)) {
                    $product->update(['category_id' => $categoryId]);
                    $updatedCount++;
                    $this->info("Updated product '{$product->name}' with category ID {$categoryId}");
                    break; // Stop checking other keywords once a match is found
                }
            }
        }

        $this->info("Category update completed. Total updated products: {$updatedCount}");
    }
}
