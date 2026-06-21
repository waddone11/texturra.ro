<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Clean `materials` table (mirror of `colors`) — replaces the legacy "Material"
 * Attribute/AttributeValue + ProductVariation tagging. Seeded from the existing
 * Material attribute_values so the data carries over (Voal / Catifea / Microfibră).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        // Seed from the legacy Material attribute_values (if present in this DB).
        $materialAttrId = DB::table('attributes')->where('name', 'Material')->value('id');
        if ($materialAttrId) {
            $values = DB::table('attribute_values')->where('attribute_id', $materialAttrId)->pluck('value')->unique();
            foreach ($values as $value) {
                $name = trim((string) $value);
                if ($name === '') {
                    continue;
                }
                DB::table('materials')->insertOrIgnore([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
