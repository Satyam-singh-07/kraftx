<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'mobile_image',
        'link',
        'sort_order',
        'status',
        'placement',
    ];

    protected $appends = [
        'desktop_url',
        'mobile_url',
    ];

    public function getDesktopUrlAttribute(): string
    {
        return $this->imageUrl($this->image ?: "banners/{$this->id}/desktop.webp");
    }

    public function getMobileUrlAttribute(): string
    {
        return $this->imageUrl($this->mobile_image ?: $this->image);
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
