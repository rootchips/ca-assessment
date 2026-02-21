<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Contracts\ProductRepositoryContract;
use App\Http\Requests\ProductIndexRequest;
use App\Support\ApiResponse;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
        *
        * @return void
     */
    public function __construct(private ProductRepositoryContract $products) {}

    /**
     * Display a paginated listing of products.
        *
        * @param ProductIndexRequest $request
        * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProductIndexRequest $request)
    {
        $validated = $request->validated();

        $perPage = (int) ($validated['per_page'] ?? 10);
        $search = $validated['search'] ?? null;
        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDirection = $validated['sort_direction'] ?? 'desc';

        $result = $this->products->paginate($search, $perPage, $sortBy, $sortDirection);

        return ApiResponse::paginated('Products fetched successfully.', ProductResource::collection($result));
    }

    /**
     * Remove all products from storage.
        *
        * @return \Illuminate\Http\JsonResponse
     */
    public function clearAll()
    {
        $this->products->clearAll();

        return ApiResponse::success('All products have been deleted.', null, 200);
    }
}
