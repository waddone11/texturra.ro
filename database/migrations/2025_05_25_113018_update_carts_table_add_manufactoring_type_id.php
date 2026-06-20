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
        //
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('manufactoring_type_id')->nullable()->after('height');
            $table->foreign('manufactoring_type_id')->references('id')->on('manufactoring_types')->nullOnDelete();
            $table->dropColumn('manopera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
