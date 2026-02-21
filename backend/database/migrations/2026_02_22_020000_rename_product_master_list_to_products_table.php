<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('product_master_list') && !Schema::hasTable('products')) {
            Schema::rename('product_master_list', 'products');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && !Schema::hasTable('product_master_list')) {
            Schema::rename('products', 'product_master_list');
        }
    }
};
