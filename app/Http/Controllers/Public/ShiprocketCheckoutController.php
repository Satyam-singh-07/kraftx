<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;

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
        
        // Prepare Cart Data for Shiprocket
        $cartData = [
            'items' => [
                [
                    'variant_id' => $request->variant_id ?? $product->id, // Shiprocket expects variant ID
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
            // The standard Shiprocket/Fastrr checkout URL
            $checkoutUrl = "https://fastrr-boost-ui.pickrr.com/?token={$token}&domain=" . $request->getHost();
            
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
