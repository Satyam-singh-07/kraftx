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
        'price',
        'stock',
        'sku',
        'image_paths',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

    protected $appends = [
        'image_path',
    ];

    public function getImagePathAttribute(): ?string
    {
        return $this->image_paths[0] ?? null;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
