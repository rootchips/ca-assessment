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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->string('type')->index();
            $table->string('brand')->index();
            $table->string('model')->index();
            $table->string('capacity')->index();
            $table->unsignedInteger('quantity')->default(0)->index();
            $table->timestamps();

            $table->index(['brand', 'model']);
            $table->index(['type', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
