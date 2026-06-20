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
        Schema::create('characteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->unsignedBigInteger('characteristic_id');
            $table->string('name');
            $table->unsignedTinyInteger('type_id');
            $table->unsignedTinyInteger('display_order')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_mandatory_for_mktp')->default(false);
            $table->boolean('allow_new_value')->default(true);
            $table->boolean('is_filter')->default(false);
            $table->json('tags')->nullable();
            $table->json('value_tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
