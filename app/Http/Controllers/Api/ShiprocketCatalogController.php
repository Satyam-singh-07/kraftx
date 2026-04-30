<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ShiprocketCatalogController extends Controller
{
    /**
     * Validate the secure catalog token from Shiprocket.
     */
    protected function validateToken(Request $request)
    {
        $headerToken = $request->header('X-Shiprocket-Catalog-Token');
        $validToken = Config::get('services.shiprocket.catalog_token');

        if (!$headerToken || $headerToken !== $validToken) {
            return false;
        }
        return true;
    }

    /**
     * Fetch products for Shiprocket Catalog in Shopify-like format.
     */
    public function fetchProducts(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);

        $query = Product::with(['images', 'collections', 'variants', 'tags'])
            ->where('status', true);

        $totalCount = $query->count();
        $products = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => [
                'total' => $totalCount,
                'products' => $this->formatProducts($products->getCollection())
            ]
        ]);
    }

    /**
     * Fetch products filtered by collection ID.
     */
    public function fetchProductsByCollection(Request $request)
    {
        // if (!$this->validateToken($request)) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        $collectionId = $request->input('collection_id');
        if (!$collectionId) {
            return response()->json(['message' => 'Collection ID is required'], 400);
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);

        $query = Product::with(['images', 'collections', 'variants', 'tags'])
            ->where('status', true)
            ->whereHas('collections', function ($q) use ($collectionId) {
                $q->where('collections.id', $collectionId);
            });

        $totalCount = $query->count();
        $products = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'data' => [
                'total' => $totalCount,
                'products' => $this->formatProducts($products->getCollection())
            ]
        ]);
    }

    /**
     * Fetch collections for Shiprocket in Shopify-like format.
     */
    public function fetchCollections(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);

        $query = Collection::where('status', true);
        $totalCount = $query->count();
        
        $collections = $query->paginate($limit, ['*'], 'page', $page);

        $formattedCollections = $collections->getCollection()->map(function ($collection) {
            return [
                'id' => (int) $collection->id,
                'updated_at' => $collection->updated_at->toIso8601String(),
                'body_html' => $collection->description,
                'handle' => $collection->slug,
                'image' => [
                    'src' => $collection->image ? asset('storage/' . $collection->image) : null,
                ],
                'title' => $collection->name,
                'created_at' => $collection->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => [
                'total' => $totalCount,
                'collections' => $formattedCollections
            ]
        ]);
    }

    /**
     * Helper to format products into the Shopify-like structure.
     */
    protected function formatProducts($products)
    {
        return $products->map(function ($product) {
            $primaryImage = $product->primary_image ? asset('storage/' . $product->primary_image->image_path) : null;
            
            // Map Variants
            $variants = $product->variants->map(function ($variant) use ($product, $primaryImage) {
                return [
                    'id' => (int) $variant->id,
                    'title' => ($variant->color ? $variant->color : '') . ($variant->size ? ' / ' . $variant->size : 'Default'),
                    'price' => number_format($variant->price ?? $product->sale_price ?? $product->price, 2, '.', ''),
                    'compare_at_price' => number_format($product->price, 2, '.', ''),
                    'sku' => $variant->sku ?? $product->sku,
                    'quantity' => (int) $variant->stock,
                    'created_at' => $variant->created_at->toIso8601String(),
                    'updated_at' => $variant->updated_at->toIso8601String(),
                    'taxable' => true,
                    'option_values' => array_filter([
                        'Color' => $variant->color,
                        'Size' => $variant->size,
                    ]),
                    'grams' => (int) ($product->weight * 1000),
                    'image' => [
                        'src' => $primaryImage
                    ],
                    'weight' => (float) $product->weight,
                    'weight_unit' => 'kg'
                ];
            });

            // If no variants exist, create a default one
            if ($variants->isEmpty()) {
                $variants->push([
                    'id' => (int) ($product->id + 900000000), // Unique long ID for default variant
                    'title' => 'Default Title',
                    'price' => number_format($product->sale_price ?? $product->price, 2, '.', ''),
                    'compare_at_price' => number_format($product->price, 2, '.', ''),
                    'sku' => $product->sku,
                    'quantity' => (int) $product->stock,
                    'created_at' => $product->created_at->toIso8601String(),
                    'updated_at' => $product->updated_at->toIso8601String(),
                    'taxable' => true,
                    'option_values' => (object)[],
                    'grams' => (int) ($product->weight * 1000),
                    'image' => [
                        'src' => $primaryImage
                    ],
                    'weight' => (float) $product->weight,
                    'weight_unit' => 'kg'
                ]);
            }

            // Map Options
            $options = [];
            $colors = $product->variants->pluck('color')->filter()->unique()->values();
            $sizes = $product->variants->pluck('size')->filter()->unique()->values();

            if ($colors->isNotEmpty()) {
                $options[] = ['name' => 'Color', 'values' => $colors];
            }
            if ($sizes->isNotEmpty()) {
                $options[] = ['name' => 'Size', 'values' => $sizes];
            }

            return [
                'id' => (int) $product->id,
                'title' => $product->name,
                'body_html' => $product->description,
                'vendor' => config('app.name', 'KraftX'),
                'product_type' => $product->collections->first()?->name ?? 'Handicrafts',
                'created_at' => $product->created_at->toIso8601String(),
                'handle' => $product->slug,
                'updated_at' => $product->updated_at->toIso8601String(),
                'tags' => $product->tags->pluck('name')->implode(', '),
                'status' => 'active',
                'variants' => $variants,
                'image' => [
                    'src' => $primaryImage
                ],
                'options' => $options
            ];
        });
    }
}
