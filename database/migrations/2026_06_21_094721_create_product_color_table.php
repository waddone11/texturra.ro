<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot product_color: a product offers N colors from the palette, with
 * per-color stock. Price stays on Product (uniform across colors — decided
 * design). Additive/reversible; does NOT touch legacy attribute_values or
 * variations (cleanup is a later phase).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_color', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained('colors')->cascadeOnDelete();
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_color');
    }
};
