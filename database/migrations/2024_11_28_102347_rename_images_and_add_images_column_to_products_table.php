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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('images', 'images_emag'); // Rename column
            $table->json('images')->nullable()->after('images_emag'); // Add new column
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('images_emag', 'images'); // Revert column rename
            $table->dropColumn('images'); // Remove new column
        });
    }
};
