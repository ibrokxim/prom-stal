<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_characteristics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('characteristic_id');
            $table->string('value')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('characteristic_id')->references('id')->on('characteristics')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_characteristic');
    }
};
