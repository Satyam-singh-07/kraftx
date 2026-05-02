<?php

namespace App\Http\Controllers\Public;

use App\Helpers\SeoHelper;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function index()
    {
        $productsModel = Product::with(['images', 'variants'])
            ->where('status', true)
            ->latest()
            ->paginate(12);

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
                'url' => route('product.show', $product->slug),
                'image' => $imageUrl,
                'hoverImage' => $hoverUrl,
                'price' => '₹' . number_format($displayPrice, 0),
                'oldPrice' => $oldPrice !== null ? '₹' . number_format($oldPrice, 0) : null,
                'hasSize' => !empty($sizes),
                'sizes' => $sizes,
                'badges' => !empty($badges) ? $badges : null,
            ];
        };

        $products = $productsModel->getCollection()->map($mapProduct);
        $productsModel->setCollection($products);

        if (request()->ajax()) {
            return view('public.products._list', ['products' => $productsModel])->render();
        }

        $seo = [
            'title' => 'All Products | ' . config('app.name', 'KraftX'),
            'description' => 'Browse all our handcrafted products at ' . config('app.name', 'KraftX') . '.',
            'canonical' => route('products.index'),
            'type' => 'website',
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'All Products', 'url' => route('products.index')],
                ]),
            ],
        ];

        return view('public.products.index', ['products' => $productsModel, 'seo' => $seo]);
    }

    public function show(string $slug)
    {
        $product = $this->productRepository->findBySlug($slug);
        
        if (!$product) {
            abort(404);
        }

        $breadcrumbs = [
            ['name' => $product->name, 'url' => '']
        ];

        // Fetch related products from the same collection
        $relatedProducts = collect();
        if ($product->collections->first()) {
            $relatedProducts = $product->collections->first()->products()
                ->with(['images', 'variants'])
                ->where('products.id', '!=', $product->id)
                ->where('status', true)
                ->take(8)
                ->get();
        }

        $reviews = $product->reviews()
            ->where('status', 'approved')
            ->latest()
            ->get();
        $product->setRelation('reviews', $reviews);

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round((float) $reviews->avg('rating'), 1) : 0;

        $ratingCounts = collect([5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]);
        foreach ($reviews->countBy('rating') as $rating => $count) {
            $ratingCounts[(int) $rating] = $count;
        }

        $ratingPercentages = $ratingCounts->map(function ($count) use ($totalReviews) {
            return $totalReviews > 0 ? (int) round(($count / $totalReviews) * 100) : 0;
        });

        $user = auth()->user();
        $hasPurchasedProduct = false;
        if ($user) {
            $hasPurchasedProduct = CartItem::where('product_id', $product->id)
                ->whereHas('cart', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->whereIn('status', ['completed', 'paid', 'delivered']);
                })
                ->exists();
        }

        $seo = SeoHelper::forModel($product, [
            'title' => ($product->seoMeta?->meta_title ?: $product->name) . ' | ' . config('app.name', 'KraftX'),
            'canonical' => route('product.show', $product->slug),
            'type' => 'product',
            'preload' => $product->images->first()?->image_path ? [[
                'href' => asset('storage/' . $product->images->first()->image_path),
                'as' => 'image',
                'fetchpriority' => 'high',
            ]] : [],
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => $product->collections->first()->name ?? 'Collection', 'url' => $product->collections->first()?->slug ? route('collection.show', $product->collections->first()->slug) : route('home')],
                    ['name' => $product->name, 'url' => route('product.show', $product->slug)],
                ]),
                SeoHelper::productSchema($product, $averageRating, $totalReviews),
            ],
        ]);

        return view('public.products.show', compact(
            'product',
            'breadcrumbs',
            'relatedProducts',
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingCounts',
            'ratingPercentages',
            'hasPurchasedProduct',
            'seo'
        ));
    }
    
    public function collectionShow(string $slug)
    {
        $collection = Collection::where('slug', $slug)->firstOrFail();
        $productsModel = $collection->products()->where('status', true)->with(['images', 'variants'])->paginate(16);

        $products = $productsModel->getCollection()->map(function($product) {
            $image = $product->images->first() ? 'storage/' . $product->images->first()->image_path : 'assets/images/product/product-placeholder.jpg';
            $hoverImage = $product->images->get(1) ? 'storage/' . $product->images->get(1)->image_path : $image;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => route('product.show', $product->slug),
                'image' => $image,
                'hoverImage' => $hoverImage,
                'price' => '₹' . number_format($product->sale_price ?? $product->price, 0),
                'oldPrice' => $product->sale_price ? '₹' . number_format($product->price, 0) : null,
                'hasSize' => $product->variants->whereNotNull('size')->isNotEmpty(),
                'sizes' => $product->variants->whereNotNull('size')->unique('size')->pluck('size')->toArray(),
                'colors' => $product->variants->whereNotNull('color')->unique('color')->map(function($v) use ($image) {
                    return [
                        'name' => $v->color,
                        'image' => $v->image_path ? 'storage/' . $v->image_path : $image,
                        'class' => ''
                    ];
                }),
                'badges' => []
            ];
        });

        // Replace the collection in the paginator with the mapped one
        $productsModel->setCollection($products);

        $seo = SeoHelper::forModel($collection, [
            'title' => ($collection->seoMeta?->meta_title ?: $collection->name) . ' Collection | ' . config('app.name', 'KraftX'),
            'description' => $collection->seoMeta?->meta_description
                ?? $collection->description
                ?? 'Browse products from the ' . $collection->name . ' collection at ' . config('app.name', 'KraftX') . '.',
            'canonical' => route('collection.show', $collection->slug),
            'type' => 'website',
            'preload' => $collection->image ? [[
                'href' => asset('storage/' . $collection->image),
                'as' => 'image',
            ]] : [],
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => $collection->name, 'url' => route('collection.show', $collection->slug)],
                ]),
                SeoHelper::collectionSchema($collection, $collection->products()->where('status', true)->take(16)->get()),
            ],
        ]);

        return view('public.collections.show', ['collection' => $collection, 'products' => $productsModel, 'seo' => $seo]);
    }

    public function collectionIndex()
    {
        $collections = Collection::where('status', 1)
            ->orderBy('sort_order')
            ->paginate(10);

        if (request()->ajax()) {
            return view('public.collections._list', compact('collections'))->render();
        }

        $seo = [
            'title' => 'All Collections | ' . config('app.name', 'KraftX'),
            'description' => 'Browse all our collections at ' . config('app.name', 'KraftX') . '.',
            'canonical' => route('collections.index'),
            'type' => 'website',
            'json_ld' => [
                SeoHelper::breadcrumbSchema([
                    ['name' => 'Home', 'url' => route('home')],
                    ['name' => 'All Collections', 'url' => route('collections.index')],
                ]),
            ],
        ];

        return view('public.collections.index', compact('collections', 'seo'));
    }
}
