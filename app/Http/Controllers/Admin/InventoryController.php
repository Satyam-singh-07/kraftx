<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    const LOW_STOCK_THRESHOLD = 10;

    /**
     * Display inventory listing.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // 'all', 'low', 'out'

        // Get products that don't have variants (manage stock at product level)
        $productsQuery = Product::whereDoesntHave('variants');
        
        // Get all variants (manage stock at variant level)
        $variantsQuery = ProductVariant::with('product');

        if ($search) {
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
            $variantsQuery->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status === 'low') {
            $productsQuery->where('stock', '>', 0)->where('stock', '<=', self::LOW_STOCK_THRESHOLD);
            $variantsQuery->where('stock', '>', 0)->where('stock', '<=', self::LOW_STOCK_THRESHOLD);
        } elseif ($status === 'out') {
            $productsQuery->where('stock', '<=', 0);
            $variantsQuery->where('stock', '<=', 0);
        }

        $products = $productsQuery->get()->map(function($p) {
            return [
                'type' => 'product',
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'stock' => $p->stock,
                'price' => $p->price,
                'image' => $p->primary_image ? asset('storage/' . $p->primary_image->image_path) : null,
                'label' => 'Base Product'
            ];
        });

        $variants = $variantsQuery->get()->map(function($v) {
            return [
                'type' => 'variant',
                'id' => $v->id,
                'name' => $v->product->name,
                'sku' => $v->sku,
                'stock' => $v->stock,
                'price' => $v->price,
                'image' => $v->product->primary_image ? asset('storage/' . $v->product->primary_image->image_path) : null,
                'label' => ($v->size ? "Size: {$v->size}" : "") . ($v->color ? " | Color: {$v->color}" : "")
            ];
        });

        $inventory = $products->concat($variants)->sortBy('stock');

        return view('admin.inventory.index', compact('inventory', 'search', 'status'));
    }

    /**
     * Update stock level.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|in:product,variant',
            'stock' => 'required|integer|min:0',
        ]);

        if ($request->type === 'product') {
            Product::where('id', $request->id)->update(['stock' => $request->stock]);
        } else {
            ProductVariant::where('id', $request->id)->update(['stock' => $request->stock]);
        }

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }

    /**
     * Get low stock alerts count for dashboard/sidebar.
     */
    public static function getLowStockCount()
    {
        $pCount = Product::whereDoesntHave('variants')->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count();
        $vCount = ProductVariant::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count();
        return $pCount + $vCount;
    }
}
