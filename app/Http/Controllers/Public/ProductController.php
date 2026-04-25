<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
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

        return view('public.products.show', compact('product', 'breadcrumbs', 'relatedProducts'));
    }
    
    public function collectionShow(string $slug)
    {
        $collection = Collection::where('slug', $slug)->firstOrFail();
        $products = $collection->products()->where('status', true)->paginate(15);
        
        return view('public.collections.show', compact('collection', 'products'));
    }
}
