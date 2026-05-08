<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShiprocketWebhookController extends Controller
{
    /**
     * Handle incoming order webhook from Shiprocket.
     */
    public function handleOrder(Request $request)
    {
        Log::info('Shiprocket Webhook Received:', $request->all());

        // 1. Signature Verification
        if (!$this->verifySignature($request)) {
            Log::warning('Shiprocket Webhook Signature Verification Failed');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        
        // The documentation shows the payload might be flat or nested under 'order'
        $orderData = $data['order'] ?? $data;

        // Use 'order_id' as per documentation page 8
        $shiprocketOrderId = $orderData['order_id'] ?? $orderData['id'] ?? null;

        if (!$shiprocketOrderId) {
            return response()->json(['message' => 'Invalid data: Missing order ID'], 400);
        }

        // Check if order already exists
        $existingOrder = Order::where('shiprocket_order_id', $shiprocketOrderId)->first();
        if ($existingOrder) {
            return response()->json(['message' => 'Order already processed'], 200);
        }

        // Map fields from documentation (Page 8)
        $email = $orderData['email'] ?? $orderData['customer_email'] ?? 'N/A';
        $phone = $orderData['phone'] ?? $orderData['customer_phone'] ?? 'N/A';
        $total = $orderData['total_amount_payable'] ?? $orderData['total'] ?? 0;
        $name = $orderData['customer_name'] ?? 'N/A';

        // Find user by email to link the order
        $user = null;
        if ($email !== 'N/A') {
            $user = User::where('email', $email)->first();
        }

        DB::beginTransaction();
        try {
            // Create the Order
            $order = Order::create([
                'user_id' => $user?->id,
                'shiprocket_order_id' => $shiprocketOrderId,
                'order_number' => 'KRAFTX-' . strtoupper(Str::random(8)),
                'total_amount' => $total,
                'subtotal' => $orderData['subtotal'] ?? $total,
                'tax_amount' => $orderData['tax'] ?? 0,
                'shipping_amount' => $orderData['shipping_charges'] ?? 0,
                'discount_amount' => $orderData['discount'] ?? 0,
                'status' => 'processing',
                'payment_method' => $orderData['payment_type'] ?? $orderData['payment_method'] ?? 'COD',
                'payment_status' => ($orderData['status'] ?? '') === 'SUCCESS' ? 'paid' : 'pending',
                'customer_name' => $name,
                'customer_email' => $email,
                'customer_phone' => $phone,
                'shipping_address' => $orderData['shipping_address'] ?? 'N/A',
                'shipping_city' => $orderData['shipping_city'] ?? 'N/A',
                'shipping_state' => $orderData['shipping_state'] ?? 'N/A',
                'shipping_pincode' => $orderData['shipping_pincode'] ?? 'N/A',
                'shipping_country' => $orderData['shipping_country'] ?? 'India',
            ]);

            // Create Order Items (Documentation shows them under cart_data.items)
            $items = $orderData['cart_data']['items'] ?? $orderData['line_items'] ?? $orderData['items'] ?? [];
            
            foreach ($items as $item) {
                $variantId = $item['variant_id'] ?? null;
                $sku = $item['sku'] ?? null;
                
                $product = null;
                $variant = null;

                if ($variantId) {
                    // Check if it's our virtual default variant ID (900000000 + product_id)
                    if ($variantId >= 900000000) {
                        $productId = $variantId - 900000000;
                        $product = Product::find($productId);
                    } else {
                        $variant = ProductVariant::with('product')->find($variantId);
                        $product = $variant?->product;
                    }
                } elseif ($sku) {
                    $product = Product::where('sku', $sku)->first();
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'variant_id' => $variant?->id,
                    'sku' => $sku ?? $variant?->sku ?? $product?->sku ?? 'N/A',
                    'name' => $item['name'] ?? $product?->name ?? 'Product',
                    'quantity' => (int) ($item['quantity'] ?? 1),
                    'price' => $item['price'] ?? 0,
                    'total' => ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1),
                ]);

                // Reduce Stock
                if ($variant) {
                    $variant->decrement('stock', (int) ($item['quantity'] ?? 1));
                } elseif ($product) {
                    $product->decrement('stock', (int) ($item['quantity'] ?? 1));
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'order_id' => $order->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shiprocket Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Verify the HMAC signature from Shiprocket.
     */
    protected function verifySignature(Request $request)
    {
        $signature = $request->header('X-Api-HMAC-SHA256');
        if (!$signature) {
            return !config('services.shiprocket.webhook_require_signature', false);
        }

        $apiSecret = config('services.shiprocket.secret');
        if (!$apiSecret) {
            return !config('services.shiprocket.webhook_require_signature', false);
        }

        $payload = $request->getContent();
        
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $apiSecret, true));

        return hash_equals($expectedSignature, $signature);
    }
}
