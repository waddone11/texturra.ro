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
            $table->unsignedInteger('buc_set')->nullable()->after('ean')->comment('Units per set');
            $table->unsignedInteger('set_bax')->nullable()->after('buc_set')->comment('Sets per box');
            $table->unsignedInteger('buc_bax')->nullable()->after('set_bax')->comment('Units per box');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['buc_set', 'set_bax', 'buc_bax']);
        });
    }
};
