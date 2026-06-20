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
            $table->integer('safir_excel_id')->nullable()->after('ean')->nullable(); // adjust 'after' column as needed
            $table->string('safir_excel_link')->nullable()->after('safir_excel_id');
            $table->string('safir_excel_name')->nullable()->after('safir_excel_id');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['safir_excel_id', 'safir_excel_link']);
        });
    }

};
