<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'published_at',
        'status',
        'is_featured',
        'is_home',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'is_home' => 'boolean',
    ];

    protected $appends = [
        'thumb_url',
        'medium_url',
        'hero_url',
    ];

    public function getThumbUrlAttribute(): string
    {
        return $this->variantUrl('thumb.webp');
    }

    public function getMediumUrlAttribute(): string
    {
        return $this->variantUrl('medium.webp');
    }

    public function getHeroUrlAttribute(): string
    {
        return $this->variantUrl('hero.webp');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->where('status', 'approved');
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'metaable');
    }

    public function getParsedContentAttribute()
    {
        $content = $this->content;
        
        return preg_replace_callback('/\[product id=(\d+)\]/', function($matches) {
            $productId = $matches[1];
            $product = \App\Models\Product::with('images')->find($productId);
            
            if ($product) {
                return view('components.blog.product-card', compact('product'))->render();
            }
            
            return '';
        }, $content);
    }

    private function variantUrl(string $filename): string
    {
        if (! $this->featured_image) {
            return asset('assets/images/blog/blog-placeholder.jpg');
        }

        if (basename($this->featured_image) === 'hero.webp') {
            return $this->imageUrl(dirname($this->featured_image) . '/' . $filename);
        }

        return $this->imageUrl($this->featured_image);
    }

    private function imageUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'assets/')) {
            return asset($path);
        }

        return Storage::url($path);
    }
}
