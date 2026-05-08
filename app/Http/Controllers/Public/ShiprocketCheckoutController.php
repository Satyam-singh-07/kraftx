<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ShiprocketCheckoutController extends Controller
{
    protected $shiprocketService;

    public function __construct(ShiprocketService $shiprocketService)
    {
        $this->shiprocketService = $shiprocketService;
    }

    /**
     * Get a checkout token for a single product (One-Click).
     */
    public function getOneClickToken(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'required'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $variantId = $request->variant_id;

        // If the variant_id is the virtual fallback (900...) but real variants exist,
        // we should use the first real variant ID instead to match Catalog behavior.
        if ($variantId >= 900000000 || !$variantId) {
            $firstVariant = $product->variants->first();
            if ($firstVariant) {
                $variantId = $firstVariant->id;
            } elseif (!$variantId) {
                $variantId = (int) ($product->id + 900000000);
            }
        }

        // Prepare Cart Data for Shiprocket
        $cartData = [
            'items' => [
                [
                    'variant_id' => (string) $variantId, 
                    'quantity' => (int) $request->quantity,
                ]
            ],
            'custom_attributes' => [
                'source' => 'one_click_checkout'
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
}
