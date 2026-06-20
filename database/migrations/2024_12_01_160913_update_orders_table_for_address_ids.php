<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rename old columns to new ones
            $table->renameColumn('shipping_address', 'shipping_address_id');
            $table->renameColumn('billing_address', 'billing_address_id');

            // Update the data types for foreign key constraints, if necessary
            $table->unsignedBigInteger('shipping_address_id')->change();
            $table->unsignedBigInteger('billing_address_id')->change();

            $table->string('payment_method')->default('')->nullable(); // Status: applied, used

            // Add foreign key constraints
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('billing_address_id')->references('id')->on('invoice_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['shipping_address_id']);
            $table->dropForeign(['billing_address_id']);

            // Rename columns back to the old ones
            $table->renameColumn('shipping_address_id', 'shipping_address');
            $table->renameColumn('billing_address_id', 'billing_address');
        });
    }
};
