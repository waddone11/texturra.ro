<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sibling products: a shared, nullable identifier linking products that are the
 * same model in different dimensions (each size is its own product — SEO long-tail).
 * NOT a FK to a "models" table (decision: simple shared id, not a separate entity).
 * Nullable — products without siblings (most, today) stay NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('model_group_id')->nullable()->index()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['model_group_id']);
            $table->dropColumn('model_group_id');
        });
    }
};
