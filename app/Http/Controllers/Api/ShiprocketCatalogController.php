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
     * Fetch products for Shiprocket Catalog.
     */
    public function fetchProducts(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $products = Product::with(['images', 'collections'])
            ->where('status', true)
            ->get();

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => strip_tags($product->description),
                'price' => (float) ($product->sale_price ?? $product->price),
                'mrp' => (float) $product->price,
                'weight' => (float) $product->weight,
                'length' => (float) $product->length,
                'width' => (float) $product->width,
                'height' => (float) $product->height,
                'hsn_code' => $product->hsn_code,
                'inventory' => $product->stock,
                'image_url' => $product->primary_image ? asset('storage/' . $product->primary_image->image_path) : null,
                'category' => $product->collections->first()?->name ?? 'Uncategorized',
                'url' => url('/products/' . $product->slug),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts
        ]);
    }

    /**
     * Fetch collections for Shiprocket.
     */
    public function fetchCollections(Request $request)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $collections = Collection::where('status', true)->get();

        $formattedCollections = $collections->map(function ($collection) {
            return [
                'id' => $collection->id,
                'name' => $collection->name,
                'slug' => $collection->slug,
                'image' => $collection->image ? asset('storage/' . $collection->image) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedCollections
        ]);
    }
}
