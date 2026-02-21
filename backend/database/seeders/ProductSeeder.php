<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's product data.
     */
    public function run(): void
    {
        $rows = [
            ['product_id' => 4450, 'type' => 'Smartphone', 'brand' => 'Apple', 'model' => 'iPhone SE', 'capacity' => '2GB/16GB', 'quantity' => 13],
            ['product_id' => 4768, 'type' => 'Smartphone', 'brand' => 'Apple', 'model' => 'iPhone SE', 'capacity' => '2GB/32GB', 'quantity' => 30],
            ['product_id' => 4451, 'type' => 'Smartphone', 'brand' => 'Apple', 'model' => 'iPhone SE', 'capacity' => '2GB/64GB', 'quantity' => 20],
            ['product_id' => 4574, 'type' => 'Smartphone', 'brand' => 'Apple', 'model' => 'iPhone SE', 'capacity' => '2GB/128GB', 'quantity' => 16],
            ['product_id' => 6039, 'type' => 'Smartphone', 'brand' => 'Apple', 'model' => 'iPhone SE (2020)', 'capacity' => '3GB/64GB', 'quantity' => 18],
        ];

        Product::query()->upsert(
            $rows,
            ['product_id'],
            ['type', 'brand', 'model', 'capacity', 'quantity']
        );
    }
}
