<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create VAT Table
        Schema::create('vats', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // VAT description (e.g., Standard 19%, Reduced 9%)
            $table->decimal('rate', 5, 2); // VAT rate (e.g., 19.00, 9.00, etc.)
            $table->timestamps(); // Created at and Updated at timestamps
        });

        // Add vat_id to Products Table
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('vat_id')->nullable()->after('price'); // Foreign key to VAT table
            $table->foreign('vat_id')->references('id')->on('vats')->onDelete('set null'); // Referential integrity
        });
    }

    public function down()
    {
        // Drop foreign key and vat_id column from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['vat_id']);
            $table->dropColumn('vat_id');
        });

        // Drop VAT table
        Schema::dropIfExists('vats');
    }
};
