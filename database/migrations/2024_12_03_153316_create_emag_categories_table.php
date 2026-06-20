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
        Schema::create('emag_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('emag_id')->unique();
            $table->unsignedBigInteger('emag_parent_id')->nullable();
            $table->boolean('is_ean_mandatory')->default(0);
            $table->boolean('is_warranty_mandatory')->default(0);
            $table->boolean('is_allowed')->default(0);
            $table->json('characteristics')->nullable();
            $table->json('family_types')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('emag_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emag_categories');
    }
};
