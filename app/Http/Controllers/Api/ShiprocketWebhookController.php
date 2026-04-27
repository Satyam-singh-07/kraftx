<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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

        // Validate the request (You can add signature verification here)
        
        $data = $request->all();
        $orderData = $data['order'] ?? null;

        if (!$orderData) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        // Check if order already exists
        $existingOrder = Order::where('shiprocket_order_id', $orderData['id'])->first();
        if ($existingOrder) {
            return response()->json(['message' => 'Order already processed'], 200);
        }

        DB::beginTransaction();
        try {
            // Create the Order
            $order = Order::create([
                'shiprocket_order_id' => $orderData['id'],
                'order_number' => 'KRAFTX-' . strtoupper(Str::random(8)),
                'total_amount' => $orderData['total'],
                'subtotal' => $orderData['subtotal'],
                'tax_amount' => $orderData['tax'] ?? 0,
                'shipping_amount' => $orderData['shipping_charges'] ?? 0,
                'discount_amount' => $orderData['discount'] ?? 0,
                'status' => 'processing',
                'payment_method' => $orderData['payment_method'],
                'payment_status' => ($orderData['payment_status'] ?? '') === 'captured' ? 'paid' : 'pending',
                'customer_name' => $orderData['customer_name'],
                'customer_email' => $orderData['customer_email'],
                'customer_phone' => $orderData['customer_phone'],
                'shipping_address' => $orderData['shipping_address'],
                'shipping_city' => $orderData['shipping_city'],
                'shipping_state' => $orderData['shipping_state'],
                'shipping_pincode' => $orderData['shipping_pincode'],
                'shipping_country' => $orderData['shipping_country'] ?? 'India',
            ]);

            // Create Order Items
            if (isset($orderData['line_items'])) {
                foreach ($orderData['line_items'] as $item) {
                    $product = Product::where('sku', $item['sku'])->first();
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product?->id,
                        'sku' => $item['sku'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);

                    // Reduce Stock
                    if ($product) {
                        $product->decrement('stock', $item['quantity']);
                    }
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
}
