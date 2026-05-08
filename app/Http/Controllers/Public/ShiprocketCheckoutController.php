<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ShiprocketCheckoutController extends Controller
{
    protected $shiprocketService;

    public function __construct(ShiprocketService $shiprocketService)
    {
        $this->shiprocketService = $shiprocketService;
    }

    /**
     * Get a checkout token for the current cart or a single product.
     */
    public function getToken(Request $request)
    {
        $items = [];

        if ($request->has('product_id')) {
            // Single Product (One-Click)
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_id' => 'required'
            ]);

            $product = Product::findOrFail($request->product_id);
            $variantId = $request->variant_id;

            if ($variantId >= 900000000 || !$variantId) {
                $firstVariant = $product->variants->first();
                if ($firstVariant) {
                    $variantId = $firstVariant->id;
                } elseif (!$variantId) {
                    $variantId = (int) ($product->id + 900000000);
                }
            }

            $items[] = [
                'variant_id' => (string) $variantId,
                'quantity' => (int) $request->quantity,
            ];
        } else {
            // Cart Checkout
            $requestData = $request->all();
            $frontendItems = $requestData['items'] ?? [];
            
            foreach ($frontendItems as $item) {
                $items[] = [
                    'variant_id' => (string) $item['variant_id'],
                    'quantity' => (int) $item['quantity'],
                ];
            }
        }

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        // Prepare Cart Data for Shiprocket
        $cartData = [
            'items' => $items,
            'custom_attributes' => [
                'source' => $request->has('product_id') ? 'one_click_checkout' : 'cart_checkout'
            ],
            'mobile_app' => false
        ];

        $result = $this->shiprocketService->getCheckoutToken($cartData);

        if (isset($result['result']['token'])) {
            $token = $result['result']['token'];
            $checkoutUrl = Arr::get($result, 'result.checkout_url')
                ?? Arr::get($result, 'result.redirect_url')
                ?? 'https://fastrr-boost-ui.pickrr.com/?customCheckoutToken=' . urlencode($token);
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'checkout_url' => $checkoutUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not generate checkout token',
            'details' => $result
        ], 500);
    }

    /**
     * Get a checkout token for a single product (Legacy support).
     */
    public function getOneClickToken(Request $request)
    {
        return $this->getToken($request);
    }
}
