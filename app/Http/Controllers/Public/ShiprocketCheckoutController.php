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
            'variant_id' => 'nullable'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Match the ID logic in ShiprocketCatalogController
        $variantId = $request->variant_id;
        if (!$variantId) {
            $variantId = (int) ($product->id + 900000000);
            // Check if there's a real first variant instead
            $firstVariant = $product->variants->first();
            if ($firstVariant) {
                $variantId = $firstVariant->id;
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
