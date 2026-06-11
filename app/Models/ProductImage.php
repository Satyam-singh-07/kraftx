<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
    ];

    protected $appends = [
        'thumb_url',
        'medium_url',
        'zoom_url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getThumbUrlAttribute(): string
    {
        return self::urlForVariant($this->image_path, 'thumb');
    }

    public function getMediumUrlAttribute(): string
    {
        return self::urlForVariant($this->image_path, 'medium');
    }

    public function getZoomUrlAttribute(): string
    {
        return self::urlForVariant($this->image_path, 'zoom');
    }

    public function getThumbPathAttribute(): string
    {
        return self::pathForVariant($this->image_path, 'thumb');
    }

    public function getMediumPathAttribute(): string
    {
        return self::pathForVariant($this->image_path, 'medium');
    }

    public function getZoomPathAttribute(): string
    {
        return self::pathForVariant($this->image_path, 'zoom');
    }

    public static function urlForVariant(?string $path, string $variant): string
    {
        $variantPath = self::pathForVariant($path, $variant);

        if (Str::startsWith($variantPath, ['http://', 'https://', '/'])) {
            return $variantPath;
        }

        if (Str::startsWith($variantPath, 'assets/')) {
            return asset($variantPath);
        }

        if (Str::startsWith($variantPath, 'storage/')) {
            return asset($variantPath);
        }

        return Storage::url($variantPath);
    }

    public static function pathForVariant(?string $path, string $variant): string
    {
        if (! $path) {
            return 'assets/images/product/product-placeholder.jpg';
        }

        if (Str::startsWith($path, ['http://', 'https://', 'assets/'])) {
            return $path;
        }

        $normalized = ltrim($path, '/');
        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        $filename = "{$variant}.webp";
        $basename = basename($normalized);

        if (in_array($basename, ['thumb.webp', 'medium.webp', 'zoom.webp'], true)) {
            return dirname($normalized) . '/' . $filename;
        }

        return $normalized;
    }
}
