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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('payment_amount', 10, 2);
            $table->string('payment_type'); // e.g., deposit, partial, full
            $table->string('payment_method'); // e.g., card, cash, bank transfer
            $table->string('payment_status')->default('paid'); // e.g., paid, pending
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
