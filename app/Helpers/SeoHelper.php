<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoHelper
{
    public static function defaults(): array
    {
        return [
            'site_name' => config('seo.site_name', config('app.name', 'KraftX')),
            'title' => config('seo.default_title', config('app.name', 'KraftX')),
            'description' => config('seo.default_description'),
            'url' => request()->fullUrl(),
            'canonical' => request()->fullUrl(),
            'type' => 'website',
            'robots' => 'index,follow',
            'image' => asset(config('seo.default_image', 'assets/images/logo/logo.png')),
            'image_alt' => config('seo.site_name', config('app.name', 'KraftX')) . ' logo',
            'locale' => str_replace('_', '-', config('app.locale', 'en')),
            'twitter_card' => 'summary_large_image',
            'json_ld' => [],
            'preload' => [],
        ];
    }

    public static function build(array $seo = []): array
    {
        $data = array_merge(self::defaults(), $seo);
        $data['title'] = trim((string) ($data['title'] ?? self::defaults()['title']));
        $data['description'] = trim(strip_tags((string) ($data['description'] ?? self::defaults()['description'])));
        $data['url'] = self::absoluteUrl($data['url'] ?? request()->fullUrl());
        $data['canonical'] = self::absoluteUrl($data['canonical'] ?? $data['url']);
        $data['image'] = self::absoluteUrl($data['image'] ?? self::defaults()['image']);
        $data['image_alt'] = trim((string) ($data['image_alt'] ?? $data['title']));
        $data['type'] = $data['type'] ?? 'website';
        $data['robots'] = $data['robots'] ?? 'index,follow';
        $data['locale'] = $data['locale'] ?? str_replace('_', '-', config('app.locale', 'en'));
        $data['json_ld'] = self::normalizeJsonLd($data['json_ld'] ?? []);
        $data['preload'] = collect($data['preload'] ?? [])->filter()->values()->all();

        return $data;
    }

    public static function renderMetaTags(array $seo = []): string
    {
        $seo = self::build($seo);
        $title = e($seo['title']);
        $description = e(Str::limit($seo['description'], 160, ''));
        $canonical = e($seo['canonical']);
        $url = e($seo['url']);
        $image = e($seo['image']);
        $imageAlt = e($seo['image_alt']);
        $type = e($seo['type']);
        $siteName = e($seo['site_name']);
        $locale = e($seo['locale']);
        $robots = e($seo['robots']);

        $tags = [
            '<title>' . $title . '</title>',
            '<meta name="description" content="' . $description . '">',
            '<meta name="robots" content="' . $robots . '">',
            '<link rel="canonical" href="' . $canonical . '">',
            '<meta property="og:locale" content="' . $locale . '">',
            '<meta property="og:site_name" content="' . $siteName . '">',
            '<meta property="og:title" content="' . $title . '">',
            '<meta property="og:description" content="' . $description . '">',
            '<meta property="og:url" content="' . $url . '">',
            '<meta property="og:type" content="' . $type . '">',
            '<meta property="og:image" content="' . $image . '">',
            '<meta property="og:image:alt" content="' . $imageAlt . '">',
            '<meta name="twitter:card" content="' . e($seo['twitter_card']) . '">',
            '<meta name="twitter:title" content="' . $title . '">',
            '<meta name="twitter:description" content="' . $description . '">',
            '<meta name="twitter:image" content="' . $image . '">',
        ];

        if (!empty($seo['keywords'])) {
            $tags[] = '<meta name="keywords" content="' . e($seo['keywords']) . '">';
        }

        return implode("\n", $tags);
    }

    public static function renderJsonLd(array $jsonLd = []): string
    {
        $jsonLd = self::normalizeJsonLd($jsonLd);

        if (empty($jsonLd)) {
            return '';
        }

        $payload = count($jsonLd) === 1
            ? $jsonLd[0]
            : [
                '@context' => 'https://schema.org',
                '@graph' => array_values($jsonLd),
            ];

        return '<script type="application/ld+json">' . json_encode(
            $payload,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        ) . '</script>';
    }

    public static function forModel($model, array $overrides = []): array
    {
        $meta = $model->seoMeta ?? null;
        $title = $meta?->meta_title
            ?? $model->title
            ?? $model->name
            ?? config('app.name', 'KraftX');
        $descSource = $meta?->meta_description
            ?? $model->excerpt
            ?? $model->short_description
            ?? $model->description
            ?? $model->content
            ?? '';

        return [
            'title' => $title,
            'description' => Str::limit(strip_tags((string) $descSource), 160, ''),
            'keywords' => $meta?->meta_keywords,
            'canonical' => $meta?->canonical_url ?? request()->fullUrl(),
            'robots' => $meta?->meta_robots ?? 'index,follow',
            'image' => $meta?->og_image
                ? self::absoluteUrl('storage/' . ltrim($meta->og_image, '/'))
                : self::modelImage($model),
            ...$overrides,
        ];
    }

    public static function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('seo.site_name', config('app.name', 'KraftX')),
            'url' => rtrim(config('app.url'), '/'),
            'logo' => asset('assets/images/logo/logo.png'),
            'email' => config('seo.support_email'),
            'telephone' => config('seo.support_phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('seo.address'),
                'addressCountry' => config('seo.country_code', 'IN'),
            ],
            'sameAs' => collect(config('seo.social', []))->filter()->values()->all(),
        ];
    }

    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('seo.site_name', config('app.name', 'KraftX')),
            'url' => rtrim(config('app.url'), '/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search.results', ['q' => '{search_term_string}']),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function webPageSchema(array $seo): array
    {
        $seo = self::build($seo);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $seo['title'],
            'description' => $seo['description'],
            'url' => $seo['canonical'],
        ];
    }

    public static function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)
                ->values()
                ->map(fn ($item, $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => self::absoluteUrl($item['url']),
                ])
                ->all(),
        ];
    }

    public static function productSchema($product, float $averageRating = 0, int $reviewCount = 0): array
    {
        $primaryImage = $product->images->first()?->image_path
            ? self::absoluteUrl('storage/' . ltrim($product->images->first()->image_path, '/'))
            : asset('assets/images/product/product-placeholder.jpg');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => [$primaryImage],
            'description' => Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 160, ''),
            'sku' => $product->sku,
            'url' => route('product.show', $product->slug),
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'INR',
                'price' => (float) ($product->sale_price ?? $product->price ?? 0),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => (int) ($product->stock ?? 0) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];

        if ($reviewCount > 0 && $averageRating > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $averageRating,
                'reviewCount' => $reviewCount,
            ];
        }

        if (method_exists($product, 'reviews')) {
            $approvedReviews = $product->relationLoaded('reviews')
                ? $product->reviews
                : collect();

            $schemaReviews = $approvedReviews
                ->where('status', 'approved')
                ->take(5)
                ->map(function ($review) {
                    return [
                        '@type' => 'Review',
                        'author' => [
                            '@type' => 'Person',
                            'name' => $review->name ?: 'Verified Buyer',
                        ],
                        'reviewBody' => Str::limit(strip_tags((string) $review->comment), 300, ''),
                        'reviewRating' => [
                            '@type' => 'Rating',
                            'ratingValue' => (int) $review->rating,
                            'bestRating' => 5,
                        ],
                        'datePublished' => $review->created_at?->toIso8601String(),
                    ];
                })
                ->values()
                ->all();

            if (!empty($schemaReviews)) {
                $schema['review'] = $schemaReviews;
            }
        }

        return $schema;
    }

    public static function collectionSchema($collection, iterable $products): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $collection->name,
            'description' => Str::limit(strip_tags($collection->description ?? ''), 160, ''),
            'url' => route('collection.show', $collection->slug),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => collect($products)
                    ->values()
                    ->map(fn ($product, $index) => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('product.show', $product->slug),
                        'name' => $product->name,
                    ])
                    ->all(),
            ],
        ];
    }

    public static function blogSchema(iterable $posts): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => config('seo.site_name', config('app.name', 'KraftX')) . ' Blog',
            'url' => route('blog.index'),
            'blogPost' => collect($posts)
                ->take(10)
                ->map(fn ($post) => [
                    '@type' => 'BlogPosting',
                    'headline' => $post->title,
                    'url' => route('blog.show', $post->slug),
                    'datePublished' => $post->published_at?->toIso8601String(),
                ])
                ->values()
                ->all(),
        ];
    }

    public static function blogPostingSchema($post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => Str::limit(strip_tags($post->excerpt ?? $post->content ?? ''), 160, ''),
            'image' => $post->featured_image ? self::absoluteUrl('storage/' . ltrim($post->featured_image, '/')) : asset('assets/images/logo/logo.png'),
            'url' => route('blog.show', $post->slug),
            'mainEntityOfPage' => route('blog.show', $post->slug),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? config('seo.site_name', config('app.name', 'KraftX')),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.site_name', config('app.name', 'KraftX')),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('assets/images/logo/logo.png'),
                ],
            ],
        ];
    }

    public static function searchResultsSchema(string $query, iterable $products): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SearchResultsPage',
            'name' => 'Search results for ' . $query,
            'url' => request()->fullUrl(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => collect($products)
                    ->values()
                    ->map(fn ($product, $index) => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('product.show', $product->slug ?? $product['slug'] ?? ''),
                        'name' => $product->name ?? $product['name'] ?? '',
                    ])
                    ->all(),
            ],
        ];
    }

    public static function itemListSchema(string $name, iterable $items, callable $map): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'itemListElement' => collect($items)
                ->values()
                ->map(fn ($item, $index) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    ...$map($item, $index),
                ])
                ->all(),
        ];
    }

    public static function faqSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($items)
                ->map(fn ($item) => [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ])
                ->values()
                ->all(),
        ];
    }

    protected static function normalizeJsonLd(array $jsonLd): array
    {
        return collect($jsonLd)
            ->flatMap(function ($item) {
                if (empty($item)) {
                    return [];
                }

                if (isset($item['@graph']) && is_array($item['@graph'])) {
                    return $item['@graph'];
                }

                return [$item];
            })
            ->filter(fn ($item) => is_array($item) && !empty($item))
            ->values()
            ->all();
    }

    protected static function absoluteUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return url($path);
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Str::startsWith($path, '/')) {
            return url($path);
        }

        return asset(ltrim($path, '/'));
    }

    protected static function modelImage($model): string
    {
        if (isset($model->featured_image) && $model->featured_image) {
            return self::absoluteUrl('storage/' . ltrim($model->featured_image, '/'));
        }

        if (method_exists($model, 'images') && $model->relationLoaded('images') && $model->images->first()?->image_path) {
            return self::absoluteUrl('storage/' . ltrim($model->images->first()->image_path, '/'));
        }

        if (isset($model->image) && $model->image) {
            if (Str::startsWith($model->image, ['assets/', '/', 'http://', 'https://'])) {
                return self::absoluteUrl($model->image);
            }

            return self::absoluteUrl(Storage::url($model->image));
        }

        return asset('assets/images/logo/logo.png');
    }
}
