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
                'priceCurrency' => 'INR',
                'price' => $product->sale_price ?? $product->price,
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ]
        ];
    }

    public static function generateJsonLdBlog($post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? 'Admin',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('assets/images/logo/logo.png'),
                ]
            ],
            'datePublished' => $post->published_at?->toIso8601String(),
            'description' => $post->excerpt ?? Str::limit(strip_tags($post->content), 160),
        ];
    }
    
    public static function getSeoTags($model): string
    {
        $meta = $model->seoMeta;
        
        $title = $meta?->meta_title ?? ($model->title ?? $model->name);
        $descSource = $model->excerpt ?? $model->description ?? $model->content ?? '';
        $description = $meta?->meta_description ?? Str::limit(strip_tags($descSource), 160);
        
        $keywords = $meta?->meta_keywords ?? '';
        $url = $meta?->canonical_url ?? request()->url();
        $image = $meta?->og_image ? asset('storage/' . $meta->og_image) : null;
        
        $tags = [
            '<title>' . e($title) . ' | ' . config('app.name') . '</title>',
            '<meta name="description" content="' . e($description) . '">',
            '<meta name="keywords" content="' . e($keywords) . '">',
            '<link rel="canonical" href="' . e($url) . '">',
            '<meta property="og:title" content="' . e($title) . '">',
            '<meta property="og:description" content="' . e($description) . '">',
            '<meta property="og:url" content="' . e($url) . '">',
            '<meta property="og:type" content="website">',
        ];
        
        if ($image) {
            $tags[] = '<meta property="og:image" content="' . e($image) . '">';
        }

        return implode("\n", $tags);
    }
}
