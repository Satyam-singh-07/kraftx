<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
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

        $product = Product::findOrFail($request->product_id);
        $variant = null;

        // Attempt to find a matching variant if color or size is provided
        if ($request->color || $request->size) {
            $query = ProductVariant::where('product_id', $product->id);
            if ($request->color) {
                $query->where('color', $request->color);
            }
            if ($request->size) {
                $query->where('size', $request->size);
            }
            $variant = $query->first();
        }

        // Get or create the cart for the current session or user
        $cart = $this->getOrCreateCart($request);

        // Calculate price (use variant price if available, else product price)
        $price = $variant && $variant->price ? $variant->price : ($product->sale_price ?? $product->price);

        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
                'price'    => $price,
            ]);
        } else {
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity'           => $request->quantity,
                'price'              => $price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart->items()->sum('quantity'),
        ]);
    }

    /**
     * Resolve the current cart or create a new one.
     */
    protected function getOrCreateCart(Request $request)
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        if ($userId) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['ip_address' => $request->ip(), 'session_id' => $sessionId]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['ip_address' => $request->ip()]
            );
        }

        return $cart;
    }
}
