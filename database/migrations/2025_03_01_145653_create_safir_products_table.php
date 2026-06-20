<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('safir_products', function (Blueprint $table) {
            $table->id();
            // Which category triggered the scrape?
            $table->unsignedBigInteger('category_id')->nullable();

            // The scraped link to the product
            $table->string('product_link')->unique();

            // Optional: store product title, slug, or anything else you want
            $table->string('product_title')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('safir_products');
    }
};
