<?php

namespace App\Http\Controllers\Public;

use App\Helpers\SeoHelper;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Collection;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

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
}
