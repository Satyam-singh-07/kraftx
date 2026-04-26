<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function results(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('home');
        }

        $productsModel = $this->productRepository->getAllPaginated(['search' => $query], 16);

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

        $productsModel->setCollection($products);

        return view('public.search.results', [
            'query' => $query,
            'products' => $productsModel
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            $trending = $this->productRepository->getTrending(5);
            return response()->json([
                'success' => true,
                'type' => 'trending',
                'products' => $this->mapSuggestions($trending)
            ]);
        }

        $products = $this->productRepository->search($query, 6);

        return response()->json([
            'success' => true,
            'type' => 'results',
            'products' => $this->mapSuggestions($products)
        ]);
    }

    protected function mapSuggestions($products)
    {
        return $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'url' => route('product.show', $product->slug),
                'price' => '₹' . number_format($product->sale_price ?? $product->price, 0),
                'old_price' => $product->sale_price ? '₹' . number_format($product->price, 0) : null,
                'image' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/product/product-placeholder.jpg'),
            ];
        });
    }
}
