<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('color_group_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Burgundia"
            $table->string('cod_css'); // e.g., "#800020"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('colors');
    }
};
