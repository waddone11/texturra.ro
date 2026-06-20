<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Faza 5b — drop the dead Safir excel-import integration tables.
 *
 * The Safir integration (6 console commands + SafirExcel/SafirProduct models +
 * the Product::safirExcels() relation) has been removed; it was never reachable
 * from the web flow (no controller/Livewire/view referenced it). These two
 * tables are the only safir_-prefixed standalone tables in the schema.
 *
 * NOTE: not yet applied to the prod dump — run manually after review (the tables
 * may still hold imported supplier data). Business-table columns named
 * safir_excel_* on the products table are intentionally NOT touched here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('safir_excel');
        Schema::dropIfExists('safir_products');
    }

    public function down(): void
    {
        if (! Schema::hasTable('safir_excel')) {
            Schema::create('safir_excel', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('safir_excel_link')->nullable();
                $table->integer('safir_excel_id')->nullable();
                $table->string('safir_excel_name')->nullable();
                $table->decimal('safir_acquisition_price', 10, 2)->nullable();
                $table->decimal('safir_sell_price', 10, 2)->nullable();
                $table->integer('safir_buc_set')->nullable();
                $table->integer('safir_set_bax')->nullable();
                $table->integer('safir_buc_bax')->nullable();
                $table->string('safir_um')->nullable();
                $table->boolean('safir_link_exist')->default(0);
                $table->boolean('safir_parsed')->default(0);
                $table->timestamp('imported_at')->nullable();
                $table->text('error_message')->nullable();
                $table->string('source_file')->nullable();
                $table->string('safir_excel_details')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('safir_products')) {
            Schema::create('safir_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('product_link')->unique();
                $table->string('product_title')->nullable();
                $table->timestamps();
            });
        }
    }
};
