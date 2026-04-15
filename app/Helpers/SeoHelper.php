<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SeoHelper
{
    public static function generateJsonLdProduct($product): array
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->images->first()?->image_path ? asset('storage/' . $product->images->first()->image_path) : null,
            'description' => $product->short_description ?? Str::limit(strip_tags($product->description), 160),
            'sku' => $product->sku,
            'offers' => [
                '@type' => 'Offer',
                'url' => url('/product/' . $product->slug),
                'priceCurrency' => 'USD', // Could be dynamic
                'price' => $product->sale_price ?? $product->price,
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ]
        ];
    }
    
    public static function getSeoTags($model): string
    {
        $meta = $model->seoMeta;
        
        $title = $meta?->meta_title ?? $model->name;
        $description = $meta?->meta_description ?? Str::limit(strip_tags($model->description ?? $model->short_description), 160);
        $keywords = $meta?->meta_keywords ?? '';
        $url = $meta?->canonical_url ?? request()->url();
        $image = $meta?->og_image ? asset('storage/' . $meta->og_image) : null;
        
        $tags = [
            '<title>' . e($title) . '</title>',
            '<meta name="description" content="' . e($description) . '">',
            '<meta name="keywords" content="' . e($keywords) . '">',
            '<link rel="canonical" href="' . e($url) . '">',
            '<meta property="og:title" content="' . e($title) . '">',
            '<meta property="og:description" content="' . e($description) . '">',
            '<meta property="og:url" content="' . e($url) . '">',
            '<meta property="og:type" content="product">',
        ];
        
        if ($image) {
            $tags[] = '<meta property="og:image" content="' . e($image) . '">';
        }

        return implode("\n", $tags);
    }
}
