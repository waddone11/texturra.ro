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
        Schema::table('emag_categories', function (Blueprint $table) {
            $table->boolean('status')->default(0)->after('family_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emag_categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
