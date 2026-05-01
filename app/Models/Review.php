<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'rating',
        'images',
        'comment',
        'status',
        'show_on_home',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'show_on_home' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
