<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redis;
use Throwable;

class UploadResource extends JsonResource
{
    public function toArray($request): array
    {
        try {
            $progress = (int) Redis::get("upload:progress:{$this->id}");
        } catch (Throwable) {
            $progress = 0;
        }

        return [
            'id' => (string) $this->id,
            'file_name' => $this->file_name,
            'status' => $this->status,
            'progress' => $progress,
            'processed_at' => optional($this->processed_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
