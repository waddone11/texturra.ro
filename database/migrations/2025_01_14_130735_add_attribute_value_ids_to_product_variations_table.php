<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributeValueIdsToProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // Modify the column type to JSON and make it nullable if needed
            $table->json('attribute_value_ids')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // Reverse the column modification (e.g., back to the previous type)
            $table->string('attribute_value_ids')->change();
        });
    }
}
