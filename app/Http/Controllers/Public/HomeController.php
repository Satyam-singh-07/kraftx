<?php

namespace App\Http\Controllers\Public;

use App\Helpers\SeoHelper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Collection;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 1)
            ->where('placement', 'home_main')
            ->orderBy('sort_order')
            ->get();

        $collections = Collection::where('status', 1)
            ->where('show_on_home', 1)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $resolveImageUrl = function($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                return $path;
            }
            if (str_starts_with($path, 'assets/')) {
                return asset($path);
            }
            return Storage::url($path);
        };

        $mapProduct = function(Product $product) use ($resolveImageUrl) {
            $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            $secondary = $product->images->first(function ($image) use ($primary) {
                return !$primary || $image->id !== $primary->id;
            });

            $fallbackImage = asset('assets/images/product/product-1.jpg');
            $imageUrl = $primary ? $resolveImageUrl($primary->image_path) : $fallbackImage;
            $hoverUrl = $secondary ? $resolveImageUrl($secondary->image_path) : $imageUrl;

            $price = (float) ($product->price ?? 0);
            $salePrice = $product->sale_price !== null ? (float) $product->sale_price : null;
            $isOnSale = $salePrice !== null && $salePrice > 0 && $price > 0 && $salePrice < $price;

            $displayPrice = $isOnSale ? $salePrice : $price;
            $oldPrice = $isOnSale ? $price : null;

            $badges = [];
            if ($isOnSale) {
                $percent = (int) round(100 * (1 - ($salePrice / $price)));
                $badges[] = ['type' => 'sale', 'text' => '-' . $percent . '%'];
            }
            if ($product->created_at && $product->created_at->gt(now()->subDays(30))) {
                $badges[] = ['type' => 'new', 'text' => 'NEW'];
            }

            $sizes = $product->variants
                ->pluck('size')
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => $product->slug ? route('product.show', $product->slug) : route('product.detail'),
                'image' => $imageUrl,
                'hoverImage' => $hoverUrl,
                'price' => '₹' . number_format($displayPrice, 0),
                'oldPrice' => $oldPrice !== null ? '₹' . number_format($oldPrice, 0) : null,
                'hasSize' => !empty($sizes),
                'sizes' => $sizes,
                'badges' => !empty($badges) ? $badges : null,
            ];
        };

        $topPicks = Product::with(['images', 'variants'])
            ->where('status', 1)
            ->where('featured', 1)
            ->orderByDesc('created_at')
            ->take(8)
            ->get()
            ->map($mapProduct);

        $trendingProducts = Product::with(['images', 'variants'])
            ->where('status', 1)
            ->where('is_trending', 1)
            ->orderByDesc('created_at')
            ->take(8)
            ->get()
            ->map($mapProduct);

        $posts = BlogPost::where('status', 1)
            ->where('is_home', 1)
            ->latest('published_at')
            ->take(3)
            ->get();

        $seo = [
            'title' => config('app.name', 'KraftX') . ' | Handcrafted Decor, Spiritual Art & Gifts',
            'description' => 'Shop handcrafted wall decor, spiritual art, trending home accents, and unique gift ideas at KraftX.',
            'canonical' => route('home'),
            'type' => 'website',
            'image' => $banners->first()?->image ? Storage::url($banners->first()->image) : asset('assets/images/logo/logo.png'),
            'preload' => $banners->first()?->image ? [[
                'href' => Storage::url($banners->first()->image),
                'as' => 'image',
                'fetchpriority' => 'high',
            ]] : [],
            'json_ld' => [
                SeoHelper::organizationSchema(),
                SeoHelper::websiteSchema(),
                SeoHelper::webPageSchema([
                    'title' => config('app.name', 'KraftX') . ' | Handcrafted Decor, Spiritual Art & Gifts',
                    'description' => 'Shop handcrafted wall decor, spiritual art, trending home accents, and unique gift ideas at KraftX.',
                    'canonical' => route('home'),
                ]),
                SeoHelper::itemListSchema('Top Picks', $topPicks, fn ($product) => [
                    'url' => $product['url'],
                    'name' => $product['name'],
                ]),
                SeoHelper::itemListSchema('Trending Products', $trendingProducts, fn ($product) => [
                    'url' => $product['url'],
                    'name' => $product['name'],
                ]),
            ],
        ];

        return view('index', compact('banners', 'collections', 'topPicks', 'trendingProducts', 'posts', 'seo'));
    }
}
