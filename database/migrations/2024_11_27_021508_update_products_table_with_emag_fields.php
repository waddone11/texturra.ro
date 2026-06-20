<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('name'); // Product brand
            $table->unsignedBigInteger('emag_category_id')->nullable()->after('category_id'); // eMAG category
            $table->string('part_number')->nullable()->after('brand_name'); // eMAG part number
            $table->decimal('sale_price', 10, 2)->nullable()->after('price'); // eMAG sale price
            $table->string('currency', 10)->nullable()->after('sale_price'); // eMAG currency
            $table->integer('warranty')->nullable()->after('currency'); // Warranty
            $table->json('images')->nullable()->change(); // Product images
            $table->json('characteristics')->nullable()->after('images'); // Characteristics
            $table->json('attachments')->nullable()->after('characteristics'); // Attachments
            $table->json('offer_details')->nullable()->after('attachments'); // Offer details
            $table->string('barcode')->nullable()->after('offer_details'); // Barcode
            $table->string('ean')->nullable()->after('barcode'); // eMAG EAN
            $table->boolean('ownership')->default(true)->after('ean'); // Ownership status
            $table->decimal('min_sale_price', 10, 2)->nullable()->after('ownership'); // Min sale price
            $table->decimal('max_sale_price', 10, 2)->nullable()->after('min_sale_price'); // Max sale price
            $table->decimal('recommended_price', 10, 2)->nullable()->after('max_sale_price'); // Recommended price
            $table->integer('general_stock')->default(0)->after('recommended_price'); // General stock
            $table->integer('status')->default(0)->after('general_stock'); // Status (active/inactive)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand_name',
                'emag_category_id',
                'part_number',
                'sale_price',
                'currency',
                'warranty',
                'characteristics',
                'attachments',
                'offer_details',
                'barcode',
                'ean',
                'ownership',
                'min_sale_price',
                'max_sale_price',
                'recommended_price',
                'general_stock',
                'status'
            ]);
        });
    }
};
