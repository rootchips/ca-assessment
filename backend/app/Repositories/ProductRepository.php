<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\ProductRepositoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Helpers\CacheBucket;
use App\Models\Product;
use Throwable;
use Log;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * Create a new repository instance.
        *
        * @return void
     */
    public function __construct(private Product $model) {}

    /**
     * Paginate products using search and sorting filters.
        *
        * @param string|null $search
        * @param int $perPage
        * @param string $sortBy
        * @param string $sortDirection
        * @return LengthAwarePaginator
     */
    public function paginate(
        ?string $search,
        int $perPage = 10,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator
    {
        $allowedSortBy = ['product_id', 'type', 'brand', 'model', 'capacity', 'quantity', 'created_at'];
        $allowedDirection = ['asc', 'desc'];
        $page = max(1, (int) request()->integer('page', 1));

        $sortBy = in_array($sortBy, $allowedSortBy, true) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), $allowedDirection, true)
            ? strtolower($sortDirection)
            : 'desc';

        return CacheBucket::remember(
            'products',
            [
                'search' => $search,
                'per_page' => $perPage,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
                'page' => $page,
            ],
            now()->addMinutes(5),
            fn () => $this->model->newQuery()
                ->search($search)
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage, ['*'], 'page', $page)
        );
    }

    /**
     * Upsert product records by product ID.
        *
        * @param Collection $data
        * @return void
     */
    public function upsert(Collection $data): void
    {
        if ($data->isEmpty()) {
            return;
        }

        try {
            $rows = $data
                ->map(fn ($item) => [
                    'product_id' => (int) ($item['product_id'] ?? $item['PRODUCT_ID'] ?? 0),
                    'type' => $item['type'] ?? $item['TYPES'] ?? $item['TYPE'] ?? null,
                    'brand' => $item['brand'] ?? $item['BRAND'] ?? null,
                    'model' => $item['model'] ?? $item['MODEL'] ?? null,
                    'capacity' => $item['capacity'] ?? $item['CAPACITY'] ?? null,
                    'quantity' => max(0, (int) ($item['quantity'] ?? $item['QUANTITY'] ?? 0)),
                ])
                ->filter(fn ($row) => $row['product_id'] > 0)
                ->values()
                ->toArray();

            if (empty($rows)) {
                return;
            }

            $this->model->upsert(
                $rows,
                uniqueBy: ['product_id'],
                update: [
                    'type',
                    'brand',
                    'model',
                    'capacity',
                    'quantity',
                ]
            );

            CacheBucket::flush('products');
        } catch (Throwable $e) {
            Log::error('Upsert failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Delete all products.
        *
        * @return void
     */
    public function clearAll(): void
    {
        $this->model->newQuery()->delete();
        CacheBucket::flush('products');
    }

    /**
     * Apply quantity updates based on status rows.
        *
        * @param Collection $data
        * @return void
     */
    public function applyStatusUpdates(Collection $data): void
    {
        if ($data->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($data) {
            $data->each(function ($item): void {
                $productId = (int) ($item['product_id'] ?? $item['PRODUCT_ID'] ?? 0);
                $status = strtolower(trim((string) ($item['status'] ?? $item['STATUS'] ?? '')));
                $quantity = max(1, (int) ($item['quantity'] ?? $item['QTY'] ?? $item['QUANTITY'] ?? 1));

                if ($productId <= 0 || !in_array($status, ['sold', 'buy'], true)) {
                    return;
                }

                $product = $this->model->newQuery()->where('product_id', $productId)->first();

                if (!$product) {
                    $product = $this->model->newQuery()->create([
                        'product_id' => $productId,
                        'type' => (string) ($item['type'] ?? $item['TYPE'] ?? $item['types'] ?? $item['TYPES'] ?? 'Unknown'),
                        'brand' => (string) ($item['brand'] ?? $item['BRAND'] ?? 'Unknown'),
                        'model' => (string) ($item['model'] ?? $item['MODEL'] ?? 'Unknown'),
                        'capacity' => (string) ($item['capacity'] ?? $item['CAPACITY'] ?? 'Unknown'),
                        'quantity' => 0,
                    ]);
                }

                $currentQuantity = (int) $product->quantity;
                $newQuantity = $status === 'sold'
                    ? max(0, $currentQuantity - $quantity)
                    : $currentQuantity + $quantity;

                $product->update(['quantity' => $newQuantity]);
            });
        });

        CacheBucket::flush('products');
    }
}
