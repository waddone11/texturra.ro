<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Faza 2 S3 — drop the dead eMAG integration tables.
 *
 * The eMAG integration (controllers, models, routes, config) has been removed.
 * These two tables are the only emag_-prefixed tables in the schema (confirmed
 * in Faza 1). No live code references them anymore.
 *
 * NOTE: not yet applied to the prod dump — run manually after review (the tables
 * may still hold synced marketplace data). Business-table columns named emag_*
 * on products/categories are intentionally NOT touched here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('emag_api_responses');
        Schema::dropIfExists('emag_categories');
    }

    public function down(): void
    {
        if (! Schema::hasTable('emag_categories')) {
            Schema::create('emag_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('emag_id');
                $table->unsignedBigInteger('emag_parent_id')->nullable();
                $table->boolean('is_ean_mandatory');
                $table->boolean('is_warranty_mandatory');
                $table->boolean('is_allowed');
                $table->json('characteristics')->nullable();
                $table->json('family_types')->nullable();
                $table->boolean('status');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('emag_api_responses')) {
            Schema::create('emag_api_responses', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->bigInteger('emag_id')->nullable();
                $table->json('response');
                $table->timestamps();
            });
        }
    }
};
