<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * RO standard VAT rose to 21% (from 19%) on 2025-08-01. Add a 21% rate row.
 *
 * We do NOT modify the existing 19% row: historical orders/invoices were computed
 * at 19% and reference products that point to it. New (and re-saved) products use
 * the 21% row; stored order totals are never recalculated.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('vats')->where('rate', 21.00)->exists()) {
            DB::table('vats')->insert([
                'name' => '21',
                'rate' => 21.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('vats')->where('rate', 21.00)->where('name', '21')->delete();
    }
};
