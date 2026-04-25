<?php

namespace App\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $short_description,
        public readonly ?string $description,
        public readonly ?string $video_url,
        public readonly ?string $perfect_placement,
        public readonly float $price,
        public readonly ?float $sale_price,
        public readonly int $stock,
        public readonly string $sku,
        public readonly bool $status,
        public readonly bool $featured,
        public readonly bool $is_trending,
        public readonly array $collection_ids = [],
        public readonly array $tag_ids = [],
        public readonly array $variants = [],
        public readonly array $seo_meta = [],
        public readonly mixed $main_image = null,
        public readonly mixed $size_weight_image = null,
        public readonly array $gallery_images = []
    ) {}

    public static function fromRequest(array $data, mixed $main_image = null, mixed $size_weight_image = null, array $gallery_images = []): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
            short_description: $data['short_description'] ?? null,
            description: $data['description'] ?? null,
            video_url: $data['video_url'] ?? null,
            perfect_placement: $data['perfect_placement'] ?? null,
            price: (float) $data['price'],
            sale_price: isset($data['sale_price']) ? (float) $data['sale_price'] : null,
            stock: (int) ($data['stock'] ?? 0),
            sku: $data['sku'] ?? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
            status: isset($data['status']) ? (bool) $data['status'] : true,
            featured: isset($data['featured']) ? (bool) $data['featured'] : false,
            is_trending: isset($data['is_trending']) ? (bool) $data['is_trending'] : false,
            collection_ids: $data['collection_ids'] ?? [],
            tag_ids: $data['tag_ids'] ?? [],
            variants: $data['variants'] ?? [],
            seo_meta: $data['seo_meta'] ?? [],
            main_image: $main_image,
            size_weight_image: $size_weight_image,
            gallery_images: $gallery_images
        );
    }
}
