<?php

namespace Tests\Feature;

use App\Contracts\ProductRepositoryContract;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Mockery;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_paginated_products_with_defaults()
    {
        $mock = Mockery::mock(ProductRepositoryContract::class);

        $items = collect([
            Product::make([
                'id' => 1,
                'product_id' => 4450,
                'type' => 'Smartphone',
                'brand' => 'Apple',
                'model' => 'iPhone SE',
                'capacity' => '2GB/16GB',
                'quantity' => 8,
            ]),
            Product::make([
                'id' => 2,
                'product_id' => 4768,
                'type' => 'Smartphone',
                'brand' => 'Apple',
                'model' => 'iPhone SE',
                'capacity' => '2GB/32GB',
                'quantity' => 15,
            ]),
        ]);

        $perPage = 10;
        $currentPage = 1;
        $total = 25;

        $paginator = new Paginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => url('/api/products')]
        );

        $mock->shouldReceive('paginate')
            ->once()
            ->with(null, $perPage, 'created_at', 'desc')
            ->andReturn($paginator);

        $this->app->instance(ProductRepositoryContract::class, $mock);

        $res = $this->getJson('/api/products');

        $res->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [[
                    'id',
                    'product_id',
                    'type',
                    'brand',
                    'model',
                    'capacity',
                    'quantity',
                    'created_at',
                    'updated_at',
                ]],
                'links' => [
                    'first','last','prev','next',
                ],
                'meta' => [
                    'current_page','from','last_page','links','path','per_page','to','total',
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Products fetched successfully.')
            ->assertJsonFragment(['product_id' => 4450])
            ->assertJsonPath('meta.per_page', $perPage)
            ->assertJsonPath('meta.current_page', $currentPage)
            ->assertJsonPath('meta.total', $total);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_respects_search_and_custom_per_page()
    {
        $mock = Mockery::mock(ProductRepositoryContract::class);

        $items = collect([
            Product::make([
                'id' => 10,
                'product_id' => 4768,
                'type' => 'Smartphone',
                'brand' => 'Apple',
                'model' => 'iPhone SE',
                'capacity' => '2GB/32GB',
                'quantity' => 19,
            ]),
        ]);

        $search = 'hat';
        $perPage = 5;
        $currentPage = 1;
        $total = 11;
        $sortBy = 'product_id';
        $sortDirection = 'asc';

        $paginator = new Paginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => url('/api/products')]
        );

        $mock->shouldReceive('paginate')
            ->once()
            ->with($search, $perPage, $sortBy, $sortDirection)
            ->andReturn($paginator);

        $this->app->instance(ProductRepositoryContract::class, $mock);

        $res = $this->getJson("/api/products?per_page={$perPage}&search={$search}&sort_by={$sortBy}&sort_direction={$sortDirection}");

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.per_page', $perPage)
            ->assertJsonPath('meta.total', $total)
            ->assertJsonFragment(['product_id' => 4768])
            ->assertJsonCount(1, 'data');
    }
}
