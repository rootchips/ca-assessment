<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product_id' => (int) $this->product_id,
            'type' => $this->type,
            'brand' => $this->brand,
            'model' => $this->model,
            'capacity' => $this->capacity,
            'quantity' => (int) $this->quantity,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}