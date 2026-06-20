<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('emag_api_responses', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type of API response, e.g., 'product', 'stock', etc.
            $table->bigInteger('emag_id')->nullable(); // eMAG ID for the entity
            $table->json('response'); // Raw JSON response from the API
            $table->timestamps(); // Timestamps for auditing
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emag_api_responses');
    }
};
