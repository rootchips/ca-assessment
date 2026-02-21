<?php
namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_id' => $this->faker->unique()->numberBetween(1000, 999999),
            'type' => $this->faker->randomElement(['Smartphone', 'Tablet', 'Laptop']),
            'brand' => $this->faker->randomElement(['Apple', 'Samsung', 'Xiaomi']),
            'model' => $this->faker->bothify('Model ##??'),
            'capacity' => $this->faker->randomElement(['64GB', '128GB', '256GB']),
            'quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}