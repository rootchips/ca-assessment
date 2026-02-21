<?php

namespace App\Models;

use App\Helpers\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product_master_list';

    protected $fillable = [
        'product_id',
        'type',
        'brand',
        'model',
        'capacity',
        'quantity',
    ];

    /**
     * Apply search filtering to the query.
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search || trim($search) === '') {
            return $query;
        }

        $normalizedSearch = TextNormalizer::clean(TextNormalizer::decode($search));
        $normalizedSearch = preg_replace('/\s+/u', ' ', trim($normalizedSearch));

        if ($normalizedSearch === null || $normalizedSearch === '') {
            return $query;
        }

        return $query->where(function (Builder $innerQuery) use ($normalizedSearch): void {
            $innerQuery->where('product_id', 'like', "%{$normalizedSearch}%")
                ->orWhere('type', 'like', "%{$normalizedSearch}%")
                ->orWhere('brand', 'like', "%{$normalizedSearch}%")
                ->orWhere('model', 'like', "%{$normalizedSearch}%")
                ->orWhere('capacity', 'like', "%{$normalizedSearch}%")
                ->orWhere('quantity', 'like', "%{$normalizedSearch}%");
        });
    }
}
