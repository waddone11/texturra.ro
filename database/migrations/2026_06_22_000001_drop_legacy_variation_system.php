<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Retire the legacy product-variation / attribute system. Colour and material —
 * the only two attributes that ever carried data — now live on the clean
 * product_color / product_material pivots, and every live read (storefront
 * cards, category filters, product detail) was switched over first.
 *
 * Dropped: product_variation_attribute_values (pivot), product_variations
 * (incl. the dead JSON attribute_value_ids column), attribute_values, attributes.
 *
 * down() recreates the empty structure for schema rollback. The DATA is not
 * restored here — a JSON dump of all four tables was taken before the drop
 * (storage/app/backups/20260622_legacy_variations__*.json).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_variation_attribute_values');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }

    public function down(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->json('extra_info')->nullable();
            $table->timestamps();
        });

        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->json('attribute_value_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('product_variation_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};
