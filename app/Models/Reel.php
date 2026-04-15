<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reel extends Model
{
    protected $fillable = [
        'title',
        'video_url',
        'thumbnail',
        'product_id',
        'sort_order',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
