<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartService
{
    /**
     * Resolve the current cart for the user or session.
     */
    public function getOrCreateCart(Request $request)
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        if ($userId) {
            $cart = Cart::where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'status' => 'active',
                    'expires_at' => Carbon::now()->addDays(7),
                    'device_type' => $this->getDeviceType($request),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } else {
            $cart = Cart::where('session_id', $sessionId)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                $cart = Cart::create([
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'status' => 'active',
                    'expires_at' => Carbon::now()->addDays(7),
                    'device_type' => $this->getDeviceType($request),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $cart;
    }

    /**
     * Add an item to the cart with snapshot price.
     */
    public function addItem(Cart $cart, int $productId, int $quantity, ?int $variantId = null)
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::find($variantId) : null;

        // Snapshot price
        $price = $variant && $variant->price ? $variant->price : ($product->sale_price ?? $product->price);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity, ['price' => $price]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }
        
        // Update cart expiry
        $cart->update(['expires_at' => Carbon::now()->addDays(7)]);

        return $cart;
    }

    /**
     * Merge guest cart into user cart upon login.
     */
    public function mergeCarts($user)
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->where('status', 'active')
            ->first();

        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            [
                'session_id' => $sessionId,
                'expires_at' => Carbon::now()->addDays(7),
                'status' => 'active'
            ]
        );

        foreach ($guestCart->items as $item) {
            $existingItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $item->quantity);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }

        // Deactivate or delete guest cart
        $guestCart->update(['status' => 'abandoned']); // Or delete()
    }

    protected function getDeviceType(Request $request)
    {
        $agent = $request->userAgent();
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $agent)) {
            return 'mobile';
        }
        return 'desktop';
    }
}
