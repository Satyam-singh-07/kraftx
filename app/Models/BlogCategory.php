<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'metaable');
    }
}
