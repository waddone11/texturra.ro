<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class PopulateDescriptionPlain extends Command
{
    protected $signature = 'products:populate-description-plain';
    protected $description = 'Populate the description_plain column with plain text content';

    public function handle()
    {
        $this->info('Populating description_plain...');

        Product::chunk(500, function ($products) {
            foreach ($products as $product) {
                $product->description_plain = strip_tags($product->description);
                $product->save();
            }
        });

        $this->info('description_plain column updated successfully.');
    }
}
