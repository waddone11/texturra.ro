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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_excluding_vat', 10, 2)->nullable()->after('total_amount');
            $table->decimal('total_vat', 10, 2)->nullable()->after('subtotal_excluding_vat');
            $table->decimal('discount', 10, 2)->nullable()->after('total_vat');
            $table->decimal('shipping_cost', 10, 2)->nullable()->after('discount');
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
            $table->dropColumn('subtotal_excluding_vat');
            $table->dropColumn('total_vat');
            $table->dropColumn('discount');
            $table->dropColumn('shipping_cost');
        });
    }
};
