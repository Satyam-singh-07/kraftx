<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class QuickAddController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['variants', 'images']);
        
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'image' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('assets/images/product/product-placeholder.jpg'),
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                    ];
                }),
                'sizes' => $product->variants->pluck('size')->unique()->filter()->values(),
                'colors' => $product->variants->pluck('color')->unique()->filter()->values(),
            ]
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
