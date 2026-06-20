<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('emag_id')->unique()->nullable(); // Unique eMAG category ID
            $table->boolean('is_ean_mandatory')->default(false); // Whether EAN is mandatory
            $table->boolean('is_warranty_mandatory')->default(false); // Whether warranty is mandatory
            $table->json('characteristics')->nullable(); // JSON field for characteristics
            $table->json('family_types')->nullable(); // JSON field for family types
            $table->boolean('is_allowed')->default(true); // Whether the category is allowed
            $table->boolean('status')->default(true); // Whether the category is allowed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'emag_id',
                'is_ean_mandatory',
                'is_warranty_mandatory',
                'characteristics',
                'family_types',
                'is_allowed',
                'status',
            ]);
        });
    }
};
