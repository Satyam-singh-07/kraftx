<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductDemandService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 10;

    public function __construct(private ProductDemandService $productDemandService)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $productsQuery = Product::with('images');
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status === 'low') {
            $productsQuery->whereBetween('stock', [1, self::LOW_STOCK_THRESHOLD]);
        } elseif ($status === 'out') {
            $productsQuery->where('stock', '<=', 0);
        }

        $inventory = $productsQuery->get()
            ->map(fn ($product) => [
                'type' => 'product',
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $product->stock,
                'price' => $product->sale_price ?? $product->price,
                'image' => $product->primary_image?->thumb_url,
                'label' => 'Product stock',
            ])
            ->sortBy('stock');

        return view('admin.inventory.index', compact('inventory', 'search', 'status'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'type' => 'required|in:product',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($request->id);
        $oldStock = (int) $product->stock;
        $product->update(['stock' => $request->stock]);
        $this->productDemandService->handleStockTransition($product->fresh(['images']), $oldStock, (int) $request->stock);

        return response()->json(['success' => true, 'message' => 'Stock updated successfully.']);
    }

    public static function getLowStockCount()
    {
        return Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count();
    }
}
