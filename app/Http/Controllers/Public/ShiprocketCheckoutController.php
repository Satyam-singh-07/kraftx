<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Api\ShiprocketWebhookController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Services\ShiprocketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        Log::error('Shiprocket checkout token response did not include a token', [
            'result' => $result,
        ]);

        return response()->json([
            'success' => false,
            'message' => Arr::get($result, 'message') ?: 'Could not generate checkout token',
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

    public function success(Request $request): RedirectResponse
    {
        Log::info('Shiprocket checkout success redirect received', [
            'query' => $request->query(),
        ]);

        $orderId = $request->query('oid') ?? $request->query('order_id');
        $status = $request->query('ost') ?? $request->query('status');

        if (!$orderId || strtoupper((string) $status) !== 'SUCCESS') {
            Log::warning('Shiprocket checkout success redirect missing successful order data', [
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return redirect()->route('track.order')->with('error', 'Payment status could not be verified yet.');
        }

        $details = $this->shiprocketService->getCheckoutOrderDetails($orderId);
        $orderData = $this->extractOrderData($details);

        if (!$orderData) {
            Log::error('Shiprocket checkout success could not fetch order details', [
                'order_id' => $orderId,
                'response' => $details,
            ]);

            return redirect()->route('track.order')->with('error', 'Order is placed, but details are still syncing.');
        }

        $orderData['order_id'] = $orderData['order_id'] ?? $orderId;
        $orderData['status'] = $orderData['status'] ?? 'SUCCESS';

        $result = app(ShiprocketWebhookController::class)->storeCheckoutOrder($orderData);

        Log::info('Shiprocket checkout success order stored', [
            'shiprocket_order_id' => $orderId,
            'local_order_id' => $result['order']->id ?? null,
            'created' => $result['created'] ?? null,
        ]);

        if (Auth::guard('web')->check()) {
            return redirect()->route('account.orders')->with('success', 'Order placed successfully.');
        }

        return redirect()->route('track.order')->with('success', 'Order placed successfully.');
    }

    protected function extractOrderData(?array $details): ?array
    {
        if (!$details) {
            return null;
        }

        $candidates = [
            Arr::get($details, 'data.order'),
            Arr::get($details, 'data'),
            Arr::get($details, 'result.order'),
            Arr::get($details, 'result'),
            $details,
        ];

        foreach ($candidates as $candidate) {
            if (is_array($candidate) && (isset($candidate['order_id']) || isset($candidate['platform_order_id']) || isset($candidate['fastrr_order_id']))) {
                return $candidate;
            }
        }

        return null;
    }
}
