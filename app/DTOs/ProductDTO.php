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
        public readonly float $weight,
        public readonly float $length,
        public readonly float $width,
        public readonly float $height,
        public readonly ?float $sale_price,
        public readonly int $stock,
        public readonly string $sku,
        public readonly ?string $hsn_code,
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
            weight: (float) ($data['weight'] ?? 0),
            length: (float) ($data['length'] ?? 0),
            width: (float) ($data['width'] ?? 0),
            height: (float) ($data['height'] ?? 0),
            sale_price: isset($data['sale_price']) ? (float) $data['sale_price'] : null,
            stock: (int) ($data['stock'] ?? 0),
            sku: $data['sku'] ?? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
            hsn_code: $data['hsn_code'] ?? null,
            status: isset($data['status']) ? (bool) $data['status'] : true,
            featured: isset($data['featured']) ? (bool) $data['featured'] : false,
            is_trending: isset($data['is_trending']) ? (bool) $data['is_trending'] : false,
            collection_ids: $data['collection_ids'] ?? [],
            tag_ids: $data['tag_ids'] ?? [],
            variants: self::normalizeVariants($data['variants'] ?? []),
            seo_meta: $data['seo_meta'] ?? [],
            main_image: $main_image,
            size_weight_image: $size_weight_image,
            gallery_images: $gallery_images
        );
    }

    protected static function normalizeVariants(array $variants): array
    {
        return collect($variants)
            ->map(function (array $variant) {
                return [
                    'id' => ($variant['id'] ?? '') === '' ? null : (int) $variant['id'],
                    'size' => trim((string) ($variant['size'] ?? '')) ?: null,
                    'color' => trim((string) ($variant['color'] ?? '')) ?: null,
                    'price' => ($variant['price'] ?? '') === '' ? null : (float) $variant['price'],
                    'stock' => (int) ($variant['stock'] ?? 0),
                    'sku' => trim((string) ($variant['sku'] ?? '')) ?: null,
                    'images' => array_values((array) ($variant['images'] ?? [])),
                    'existing_image_paths' => self::decodeExistingImagePaths($variant['existing_image_paths'] ?? null),
                ];
            })
            ->filter(fn (array $variant) => $variant['size'] || $variant['color'] || $variant['price'] !== null || $variant['stock'] > 0 || $variant['sku'] || $variant['images'])
            ->values()
            ->all();
    }

    protected static function decodeExistingImagePaths(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values(array_filter($decoded)) : [];
    }
}
