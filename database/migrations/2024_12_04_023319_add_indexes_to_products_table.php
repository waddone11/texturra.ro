<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Add the column only if it doesn't already exist
            if (!Schema::hasColumn('products', 'description_plain')) {
                $table->text('description_plain')->nullable();
            }

            // Add the FULLTEXT index to the plain-text description
            $table->fullText('description_plain', 'products_description_plain_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'description_plain')) {
                $table->dropFullText('products_description_plain_fulltext');
                $table->dropColumn('description_plain');
            }
        });
    }
};
