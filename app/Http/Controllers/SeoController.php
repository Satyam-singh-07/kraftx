<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Collection;
use App\Models\Deal;
use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            ['loc' => route('blog.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('contact.us'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('privacy.policy'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('terms.conditions'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('shipping.policy'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('return.refund'), 'changefreq' => 'monthly', 'priority' => '0.4'],
        ]);

        $productUrls = Product::query()
            ->where('status', true)
            ->select(['slug', 'updated_at'])
            ->get()
            ->map(fn ($product) => [
                'loc' => route('product.show', $product->slug),
                'lastmod' => optional($product->updated_at)->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ]);

        $collectionUrls = Collection::query()
            ->where('status', true)
            ->select(['slug', 'updated_at'])
            ->get()
            ->map(fn ($collection) => [
                'loc' => route('collection.show', $collection->slug),
                'lastmod' => optional($collection->updated_at)->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

        $blogUrls = BlogPost::query()
            ->where('status', true)
            ->select(['slug', 'updated_at', 'published_at'])
            ->get()
            ->map(fn ($post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => optional($post->updated_at ?? $post->published_at)->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);

        $dealUrls = Deal::query()
            ->where('status', true)
            ->select(['slug', 'updated_at'])
            ->get()
            ->map(fn ($deal) => [
                'loc' => route('deals.show', $deal->slug),
                'lastmod' => optional($deal->updated_at)->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.7',
            ]);

        $xml = view('seo.sitemap', [
            'urls' => $urls
                ->merge($productUrls)
                ->merge($collectionUrls)
                ->merge($blogUrls)
                ->merge($dealUrls),
        ])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /search',
            'Disallow: /track-order',
            'Disallow: /checkout',
            'Disallow: /cart',
            'Disallow: /api/',
            '',
            'Sitemap: ' . route('seo.sitemap'),
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
