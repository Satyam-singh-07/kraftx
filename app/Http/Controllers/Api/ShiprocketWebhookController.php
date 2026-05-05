<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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

        // Validate the request (You can add signature verification here)
        
        $data = $request->all();
        $orderData = $data['order'] ?? $data; // Handle both nested and flat structures if they vary

        if (!isset($orderData['id'])) {
            return response()->json(['message' => 'Invalid data: Missing order ID'], 400);
        }

        // Check if order already exists
        $existingOrder = Order::where('shiprocket_order_id', $orderData['id'])->first();
        if ($existingOrder) {
            return response()->json(['message' => 'Order already processed'], 200);
        }

        // Find user by email to link the order
        $user = null;
        if (!empty($orderData['customer_email'])) {
            $user = User::where('email', $orderData['customer_email'])->first();
        }

        DB::beginTransaction();
        try {
            // Create the Order
            $order = Order::create([
                'user_id' => $user?->id,
                'shiprocket_order_id' => $orderData['id'],
                'order_number' => 'KRAFTX-' . strtoupper(Str::random(8)),
                'total_amount' => $orderData['total'],
                'subtotal' => $orderData['subtotal'] ?? $orderData['total'],
                'tax_amount' => $orderData['tax'] ?? 0,
                'shipping_amount' => $orderData['shipping_charges'] ?? 0,
                'discount_amount' => $orderData['discount'] ?? 0,
                'status' => 'processing',
                'payment_method' => $orderData['payment_method'] ?? 'COD',
                'payment_status' => ($orderData['payment_status'] ?? '') === 'captured' ? 'paid' : 'pending',
                'customer_name' => $orderData['customer_name'] ?? 'N/A',
                'customer_email' => $orderData['customer_email'] ?? 'N/A',
                'customer_phone' => $orderData['customer_phone'] ?? 'N/A',
                'shipping_address' => $orderData['shipping_address'] ?? 'N/A',
                'shipping_city' => $orderData['shipping_city'] ?? 'N/A',
                'shipping_state' => $orderData['shipping_state'] ?? 'N/A',
                'shipping_pincode' => $orderData['shipping_pincode'] ?? 'N/A',
                'shipping_country' => $orderData['shipping_country'] ?? 'India',
            ]);

            // Create Order Items
            $lineItems = $orderData['line_items'] ?? $orderData['items'] ?? [];
            foreach ($lineItems as $item) {
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

            DB::commit();
            return response()->json(['success' => true, 'order_id' => $order->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shiprocket Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
}
