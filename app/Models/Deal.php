<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
    ];

    public function offerRules(): HasMany
    {
        return $this->hasMany(OfferRule::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'deal_product');
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'metaable');
    }
    
    public function isValid(): bool
    {
        if (!$this->status) return false;
        $now = now();
        if ($this->start_date && $this->start_date > $now) return false;
        if ($this->end_date && $this->end_date < $now) return false;
        return true;
    }
}
