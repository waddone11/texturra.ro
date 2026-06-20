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
        Schema::table('carts', function (Blueprint $table) {
            // Make quantity nullable
            $table->unsignedInteger('quantity')->nullable()->change();

            // Add custom product fields (nullable by default)
            $table->decimal('length', 6, 2)->nullable()->after('price');
            $table->decimal('height', 6, 2)->nullable()->after('length');
            $table->string('manopera')->nullable()->after('height');

            // Add pieces (default to 1)
            $table->unsignedTinyInteger('pieces')->nullable()->after('manopera');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['length', 'height', 'manopera', 'pieces']);

            // Restore quantity to not nullable if needed
            $table->unsignedInteger('quantity')->nullable(false)->change();
        });
    }
};
