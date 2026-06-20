<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products_old', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('emag_price', 10, 2)->nullable();
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('emag_category_id')->nullable();
            $table->json('images')->nullable();
            $table->json('images_emag')->nullable();
            $table->json('images_emag2')->nullable();
            $table->string('product_code')->nullable();
            $table->string('emag_id')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('part_number')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('warranty')->nullable();
            $table->unsignedBigInteger('family_type_id')->nullable();
            $table->json('characteristics')->nullable();
            $table->json('attachments')->nullable();
            $table->json('offer_details')->nullable();
            $table->string('barcode')->nullable();
            $table->string('ean')->nullable();
            $table->string('ownership')->nullable();
            $table->decimal('min_sale_price', 10, 2)->nullable();
            $table->decimal('max_sale_price', 10, 2)->nullable();
            $table->decimal('recommended_price', 10, 2)->nullable();
            $table->integer('general_stock')->default(0);
            $table->string('status')->nullable();
            $table->unsignedBigInteger('vat_id')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products_old');
    }
};

