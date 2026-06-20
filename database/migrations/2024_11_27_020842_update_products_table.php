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
            $table->dropColumn('stock'); // Remove stock field as it's moved to product_stocks table
            $table->decimal('emag_price', 10, 2)->nullable()->after('price'); // eMAG-specific price
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('emag_price'); // Commission for eMAG
            $table->unsignedBigInteger('emag_id')->nullable()->after('product_code'); // eMAG-specific product ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0); // Restore stock field
            $table->dropColumn(['emag_price', 'commission_percentage', 'emag_id']);
        });
    }
};
