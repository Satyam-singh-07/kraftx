<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'color'      => 'nullable|string',
            'size'       => 'nullable|string',
        ]);

        $product = Product::with('variants')->whereKey($request->product_id)->where('status', true)->firstOrFail();
        $quantity = (int) $request->quantity;
        $variant = null;

        if ($request->filled('variant_id')) {
            $variant = ProductVariant::where('product_id', $product->id)->whereKey($request->integer('variant_id'))->first();
        } elseif ($request->color || $request->size) {
            $query = ProductVariant::where('product_id', $product->id);
            if ($request->color) {
                $query->where('color', $request->color);
            }
            if ($request->size) {
                $query->where('size', $request->size);
            }
            $variant = $query->first();
        }

        $availableStock = $variant ? (int) $variant->stock : (int) $product->stock;
        if ($availableStock <= 0) {
            throw ValidationException::withMessages([
                'product_id' => 'This selection is currently out of stock.',
            ]);
        }

        if ($quantity > $availableStock) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$availableStock} available for this selection.",
            ]);
        }

        $cart = $this->cartService->getOrCreateCart($request);
        $this->cartService->addItem($cart, $product->id, $quantity, $variant?->id);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart->fresh('items')->items->sum('quantity'),
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
        $cartItem->load(['product', 'variant']);
        $availableStock = $cartItem->variant ? (int) $cartItem->variant->stock : (int) $cartItem->product->stock;
        if ((int) $request->quantity > $availableStock) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$availableStock} available for this selection.",
            ]);
        }

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
