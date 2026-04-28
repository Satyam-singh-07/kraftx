<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Fetch the current cart data.
     */
    public function fetch(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart($request);
        $items = $cart->items()->with(['product.images', 'variant'])->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'cart_count' => $items->sum('quantity'),
            'total' => $items->sum(fn($item) => $item->price * $item->quantity),
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'color'      => 'nullable|string',
            'size'       => 'nullable|string',
        ]);

        $variantId = null;
        if ($request->color || $request->size) {
            $query = ProductVariant::where('product_id', $request->product_id);
            if ($request->color) {
                $query->where('color', $request->color);
            }
            if ($request->size) {
                $query->where('size', $request->size);
            }
            $variantId = $query->first()?->id;
        }

        $cart = $this->cartService->getOrCreateCart($request);
        $this->cartService->addItem($cart, $request->product_id, $request->quantity, $variantId);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart->items->sum('quantity'),
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($request->item_id);
        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $cartItem->cart;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'cart_count' => $cart->items->sum('quantity'),
            'total' => $cart->items->sum(fn($item) => $item->price * $item->quantity),
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = CartItem::findOrFail($request->item_id);
        $cart = $cartItem->cart;
        $cartItem->delete();

        // Refresh items collection
        $cart->load('items');

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $cart->items->sum('quantity'),
            'total' => $cart->items->sum(fn($item) => $item->price * $item->quantity),
        ]);
    }

    /**
     * Fetch recommended products.
     */
    public function recommendations()
    {
        $products = Product::where('status', true)
            ->with('images')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ])->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
