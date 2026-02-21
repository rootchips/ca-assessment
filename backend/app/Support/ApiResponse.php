<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiResponse
{
    /**
     * Build a successful JSON response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return JsonResponse
     */
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Build a successful paginated JSON response.
     *
     * @param string $message
     * @param AnonymousResourceCollection $collection
     * @param int $status
     * @return JsonResponse
     */
    public static function paginated(string $message, AnonymousResourceCollection $collection, int $status = 200): JsonResponse
    {
        $payload = $collection->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $payload['data'] ?? [],
            'meta' => $payload['meta'] ?? null,
            'links' => $payload['links'] ?? null,
        ], $status);
    }

    /**
     * Build an error JSON response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $errors
     * @return JsonResponse
     */
    public static function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
