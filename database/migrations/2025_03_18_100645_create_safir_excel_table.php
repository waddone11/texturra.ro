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
        Schema::create('safir_excel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(); // may link to products table later
            $table->string('safir_excel_link')->nullable(); // could be a URL string
            $table->integer('safir_excel_id')->nullable();
            $table->string('safir_excel_name')->nullable();
            $table->decimal('safir_acquisition_price', 10, 2)->nullable();
            $table->decimal('safir_sell_price', 10, 2)->nullable();
            $table->integer('safir_buc_set')->nullable();
            $table->integer('safir_set_bax')->nullable();
            $table->integer('safir_buc_bax')->nullable();
            $table->string('safir_um')->nullable();
            $table->boolean('safir_link_exist')->default(0);
            $table->boolean('safir_parsed')->default(0);
            $table->timestamp('imported_at')->nullable(); // when the excel row was imported
            $table->text('error_message')->nullable();    // to log any error during processing
            $table->string('source_file')->nullable();      // name of the excel file or path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safir_excel');
    }
};
