<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('color_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Roșu & Burgundia"
            $table->string('image_path'); // path to image, e.g., storage/colors/rosu_burgundia.avif
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('color_groups');
    }
};
