<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Modify the `status` column to add new enum values
            $table->enum('status', ['pending', 'placed', 'processing', 'completed', 'canceled'])
                ->default('pending')
                ->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled'])
                ->default('pending')
                ->change();
        });
    }
};
