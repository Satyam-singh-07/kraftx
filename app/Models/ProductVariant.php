<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'items_count',
        'linked_skus',
    ];

    protected $casts = [
        'linked_skus' => 'array',
        'items_count' => 'integer',
    ];

    public function linkedProducts()
    {
        $skus = array_values(array_filter((array) $this->linked_skus));

        return Product::with('images')
            ->whereIn('sku', $skus)
            ->where('status', true)
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->sku, $skus, true))
            ->values();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
