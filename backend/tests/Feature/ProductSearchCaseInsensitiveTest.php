<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchCaseInsensitiveTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_finds_iphone_se_with_lowercase_query(): void
    {
        Product::factory()->create([
            'product_id' => 1001,
            'type' => 'Smartphone',
            'brand' => 'Apple',
            'model' => 'iPhone SE',
            'capacity' => '64GB',
            'quantity' => 5,
        ]);

        $response = $this->getJson('/api/products?search=iphone se&per_page=10&sort_by=product_id&sort_direction=asc');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.model', 'iPhone SE');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_finds_iphone_se_with_uppercase_query(): void
    {
        Product::factory()->create([
            'product_id' => 1002,
            'type' => 'Smartphone',
            'brand' => 'Apple',
            'model' => 'iPhone SE',
            'capacity' => '128GB',
            'quantity' => 8,
        ]);

        $response = $this->getJson('/api/products?search=IPHONE SE&per_page=10&sort_by=product_id&sort_direction=asc');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.model', 'iPhone SE');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_searches_using_model_column_even_when_other_columns_do_not_match(): void
    {
        Product::factory()->create([
            'product_id' => 2001,
            'type' => 'Tablet',
            'brand' => 'BrandA',
            'model' => 'Galaxy Ultra X',
            'capacity' => '128GB',
            'quantity' => 3,
        ]);

        Product::factory()->create([
            'product_id' => 2002,
            'type' => 'Tablet',
            'brand' => 'BrandA',
            'model' => 'Different Model',
            'capacity' => '128GB',
            'quantity' => 3,
        ]);

        $response = $this->getJson('/api/products?search=ultra x&per_page=10&sort_by=product_id&sort_direction=asc');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_id', 2001)
            ->assertJsonPath('data.0.model', 'Galaxy Ultra X');
    }


}
