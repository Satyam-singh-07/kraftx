<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        Log::info('Shiprocket Checkout Order Webhook Received:', $request->all());

        if (!$this->verifyRequest($request)) {
            Log::warning('Shiprocket Checkout Order Webhook Verification Failed');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orderData = $request->input('order', $request->all());
        $shiprocketOrderId = $orderData['order_id']
            ?? $orderData['platform_order_id']
            ?? $orderData['fastrr_order_id']
            ?? null;

        if (blank($shiprocketOrderId)) {
            Log::warning('Shiprocket Checkout Order Webhook missing order_id', ['payload' => $orderData]);
            return response()->json(['message' => 'Invalid data: Missing order_id'], 422);
        }

        try {
            $result = $this->storeCheckoutOrder($orderData);

            return response()->json([
                'success' => true,
                'message' => $result['created'] ? 'Order created' : 'Order updated',
                'order_id' => $result['order']->id,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Shiprocket Checkout Order Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $orderData,
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Handle delivery/tracking updates from Shiprocket API webhooks.
     */
    public function handleDeliveryStatus(Request $request)
    {
        $trackingData = $request->all();
        Log::info('Shiprocket Delivery Status Webhook Received:', $trackingData);

        if (!$this->verifySecurityToken($request)) {
            Log::warning('Shiprocket Delivery Status Webhook Verification Failed');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $order = $this->findOrderForTrackingPayload($trackingData);

            if (!$order) {
                Log::warning('Shiprocket Delivery Status Webhook could not match order', [
                    'payload' => $trackingData,
                ]);

                // Shiprocket expects a 200 response. Returning 200 here prevents retry loops
                // while preserving the unmatched payload in logs for debugging.
                return response()->json([
                    'success' => true,
                    'message' => 'Webhook received, but no matching order was found.',
                ], 200);
            }

            $status = $trackingData['current_status']
                ?? $trackingData['shipment_status']
                ?? $trackingData['status']
                ?? null;
            $statusId = $trackingData['current_status_id']
                ?? $trackingData['shipment_status_id']
                ?? $trackingData['status_id']
                ?? null;
            $timestamp = $trackingData['current_timestamp']
                ?? $trackingData['status_time']
                ?? $trackingData['updated_at']
                ?? $trackingData['event_time']
                ?? now();

            $payload = is_array($order->shiprocket_payload) ? $order->shiprocket_payload : [];
            $payload['latest_tracking_webhook'] = $trackingData;

            $mappedStatus = $this->mapShipmentStatus($status);

            $order->fill([
                'awb_code' => $trackingData['awb'] ?? $trackingData['awb_code'] ?? $order->awb_code,
                'courier_name' => $trackingData['courier_name'] ?? $trackingData['courier'] ?? $order->courier_name,
                'shipment_status' => $status ?? $order->shipment_status,
                'shipment_status_id' => $statusId ?? $order->shipment_status_id,
                'shipment_status_updated_at' => $this->dateTimeOrNull($timestamp) ?? now(),
                'shipment_track_url' => $trackingData['track_url']
                    ?? $trackingData['tracking_url']
                    ?? $trackingData['tracking_link']
                    ?? $order->shipment_track_url,
                'delivered_at' => $mappedStatus === 'delivered'
                    ? ($this->dateTimeOrNull($timestamp) ?? now())
                    : $order->delivered_at,
                'shiprocket_payload' => $payload,
            ]);

            if ($mappedStatus) {
                $order->status = $mappedStatus;
            }

            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Delivery status updated',
                'order_id' => $order->id,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Shiprocket Delivery Status Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $trackingData,
            ]);

            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function storeCheckoutOrder(array $orderData): array
    {
        $shiprocketOrderId = $orderData['order_id']
            ?? $orderData['platform_order_id']
            ?? $orderData['fastrr_order_id']
            ?? null;

        if (blank($shiprocketOrderId)) {
            throw new \InvalidArgumentException('Missing Shiprocket order_id.');
        }

        $shippingAddress = is_array($orderData['shipping_address'] ?? null) ? $orderData['shipping_address'] : [];
        $email = $shippingAddress['email'] ?? $orderData['email'] ?? $orderData['customer_email'] ?? 'N/A';
        $phone = $shippingAddress['phone'] ?? $orderData['phone'] ?? $orderData['customer_phone'] ?? 'N/A';
        $name = $this->customerName($shippingAddress, $orderData);

        $user = null;
        if ($email !== 'N/A') {
            $user = User::where('email', $email)->first();
        }

        return DB::transaction(function () use ($orderData, $shiprocketOrderId, $shippingAddress, $email, $phone, $name, $user) {
            $order = Order::where('shiprocket_order_id', $shiprocketOrderId)->lockForUpdate()->first();
            $created = !$order;

            if (!$order) {
                $order = new Order([
                    'shiprocket_order_id' => $shiprocketOrderId,
                    'order_number' => $this->makeOrderNumber($orderData),
                ]);
            }

            $order->fill([
                'user_id' => $user?->id,
                'platform_order_id' => $orderData['platform_order_id'] ?? null,
                'fastrr_order_id' => $orderData['fastrr_order_id'] ?? null,
                'cart_id' => $orderData['cart_id'] ?? null,
                'total_amount' => $this->money($orderData['total_amount_payable'] ?? $orderData['total'] ?? 0),
                'subtotal' => $this->money($orderData['subtotal_price'] ?? $orderData['subtotal'] ?? $orderData['total_amount_payable'] ?? 0),
                'tax_amount' => $orderData['tax'] ?? 0,
                'shipping_amount' => $this->money($orderData['shipping_charges'] ?? 0) + $this->money($orderData['cod_charges'] ?? 0),
                'discount_amount' => $this->money($orderData['total_discount'] ?? $orderData['coupon_discount'] ?? $orderData['discount'] ?? 0),
                'status' => $this->mapOrderStatus($orderData['status'] ?? null),
                'checkout_status' => $orderData['status'] ?? null,
                'source' => $orderData['source'] ?? null,
                'shipping_plan' => $orderData['shipping_plan'] ?? null,
                'rto_prediction' => $orderData['rto_prediction'] ?? null,
                'estimated_delivery_date' => $this->dateOrNull($orderData['edd'] ?? null),
                'shiprocket_order_created_at' => $this->dateTimeOrNull($orderData['order_created_date'] ?? null),
                'payment_method' => $this->paymentMethod($orderData),
                'payment_status' => $this->mapPaymentStatus($orderData),
                'customer_name' => $name,
                'customer_email' => $email,
                'customer_phone' => $phone,
                'shipping_address' => $this->addressLine($shippingAddress) ?: 'N/A',
                'shipping_city' => $shippingAddress['city'] ?? $orderData['shipping_city'] ?? 'N/A',
                'shipping_state' => $shippingAddress['state'] ?? $orderData['shipping_state'] ?? 'N/A',
                'shipping_pincode' => $shippingAddress['pincode'] ?? $orderData['shipping_pincode'] ?? 'N/A',
                'shipping_country' => $shippingAddress['country'] ?? $orderData['shipping_country'] ?? 'India',
                'shipping_address_data' => $shippingAddress ?: null,
                'billing_address_data' => is_array($orderData['billing_address'] ?? null) ? $orderData['billing_address'] : null,
                'payments' => $orderData['payments'] ?? [],
                'coupon_codes' => $orderData['coupon_codes'] ?? [],
                'discount_detail' => $orderData['discount_detail'] ?? null,
                'shiprocket_tags' => $orderData['tags'] ?? [],
                'shiprocket_payload' => $orderData,
            ]);
            $order->save();

            $shouldCreateItems = $created || !$order->items()->exists();
            if ($shouldCreateItems) {
                $order->items()->delete();
                $this->createItems($order, $orderData, $created);
            }

            return ['order' => $order, 'created' => $created];
        });
    }

    protected function verifyRequest(Request $request): bool
    {
        return $this->verifySecurityToken($request) && $this->verifySignature($request);
    }

    protected function verifySecurityToken(Request $request): bool
    {
        $expectedToken = config('services.shiprocket.webhook_token');
        $providedToken = $request->header('x-api-key');

        Log::info('Shiprocket Webhook Security Token Check:', [
            'expected' => $expectedToken ? 'set' : 'not set',
            'provided' => $providedToken ? 'set' : 'not set'
        ]);

        if (!$expectedToken) {
            return true;
        }

        return $providedToken && hash_equals($expectedToken, $providedToken);
    }

    protected function verifySignature(Request $request): bool
    {
        $signature = $request->header('X-Api-HMAC-SHA256');
        $requireSignature = config('services.shiprocket.webhook_require_signature', false);

        Log::info('Shiprocket Webhook Signature Check:', [
            'signature_present' => !!$signature,
            'require_signature' => $requireSignature
        ]);

        if (!$signature) {
            return !$requireSignature;
        }

        $apiSecret = config('services.shiprocket.secret');
        if (!$apiSecret) {
            Log::warning('Shiprocket Webhook: Signature provided but SHIPROCKET_API_SECRET is not set');
            return !$requireSignature;
        }

        $payload = $request->getContent();
        
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $apiSecret, true));
        $isValid = hash_equals($expectedSignature, $signature);

        if (!$isValid) {
            Log::warning('Shiprocket Webhook Signature Mismatch', [
                'expected' => $expectedSignature,
                'provided' => $signature
            ]);
        }

        return $isValid;
    }

    protected function createItems(Order $order, array $orderData, bool $decrementStock): void
    {
        $items = Arr::get($orderData, 'cart_data.items', $orderData['line_items'] ?? $orderData['items'] ?? []);

        foreach ($items as $item) {
            $variantId = $item['variant_id'] ?? null;
            $sku = $item['sku'] ?? null;
            $quantity = max((int) ($item['quantity'] ?? 1), 1);

            [$product, $variant] = $this->resolveProductAndVariant($variantId, $sku);
            $price = $this->money($item['price'] ?? $variant?->price ?? $product?->sale_price ?? $product?->price ?? 0);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product?->id,
                'variant_id' => $variant?->id,
                'sku' => $sku ?? $variant?->sku ?? $product?->sku ?? 'N/A',
                'name' => $item['name'] ?? $product?->name ?? 'Product',
                'quantity' => $quantity,
                'price' => $price,
                'total' => $price * $quantity,
            ]);

            if ($decrementStock && $this->mapOrderStatus($orderData['status'] ?? null) !== 'cancelled') {
                if ($variant) {
                    $variant->decrement('stock', $quantity);
                } elseif ($product) {
                    $product->decrement('stock', $quantity);
                }
            }
        }
    }

    protected function resolveProductAndVariant($variantId, ?string $sku): array
    {
        $product = null;
        $variant = null;

        if ($variantId !== null && $variantId !== '') {
            if (is_numeric($variantId) && (int) $variantId >= 900000000) {
                $product = Product::find((int) $variantId - 900000000);
            } else {
                $variant = ProductVariant::with('product')->find($variantId);
                $product = $variant?->product;
            }
        }

        if (!$product && $sku) {
            $variant = ProductVariant::with('product')->where('sku', $sku)->first();
            $product = $variant?->product ?? Product::where('sku', $sku)->first();
        }

        return [$product, $variant];
    }

    protected function makeOrderNumber(array $orderData): string
    {
        $sourceId = $orderData['fastrr_order_id'] ?? $orderData['order_id'] ?? null;
        $candidate = $sourceId ? 'KRAFTX-' . $sourceId : 'KRAFTX-' . strtoupper(Str::random(8));

        if (!Order::where('order_number', $candidate)->exists()) {
            return $candidate;
        }

        return 'KRAFTX-' . strtoupper(Str::random(8));
    }

    protected function customerName(array $shippingAddress, array $orderData): string
    {
        $name = trim(implode(' ', array_filter([
            $shippingAddress['first_name'] ?? null,
            $shippingAddress['last_name'] ?? null,
        ])));

        return $name ?: ($orderData['customer_name'] ?? 'N/A');
    }

    protected function addressLine(array $address): string
    {
        return trim(implode("\n", array_filter([
            $address['line1'] ?? null,
            $address['line2'] ?? null,
            $address['landmark'] ?? null,
        ])));
    }

    protected function paymentMethod(array $orderData): string
    {
        return $orderData['payment_type']
            ?? Arr::get($orderData, 'payments.0.payment_method')
            ?? $orderData['payment_method']
            ?? 'N/A';
    }

    protected function mapOrderStatus(?string $status): string
    {
        return match (Str::lower((string) $status)) {
            'success' => 'processing',
            'cancelled', 'canceled', 'failed', 'failure' => 'cancelled',
            default => 'pending',
        };
    }

    protected function mapPaymentStatus(array $orderData): string
    {
        $status = Str::lower((string) ($orderData['payment_status'] ?? Arr::get($orderData, 'payments.0.payment_status')));

        if ($status === 'success' || $status === 'paid') {
            return 'paid';
        }

        if ($status === 'failed' || $status === 'failure') {
            return 'failed';
        }

        if ($status === 'refunded') {
            return 'refunded';
        }

        return 'pending';
    }

    protected function findOrderForTrackingPayload(array $trackingData): ?Order
    {
        $awb = $trackingData['awb'] ?? $trackingData['awb_code'] ?? null;
        $orderId = $trackingData['order_id']
            ?? $trackingData['shiprocket_order_id']
            ?? $trackingData['sr_order_id']
            ?? null;
        $channelOrderId = $trackingData['channel_order_id']
            ?? $trackingData['platform_order_id']
            ?? $trackingData['order_number']
            ?? null;

        if (blank($awb) && blank($orderId) && blank($channelOrderId)) {
            return null;
        }

        return Order::query()
            ->when($awb, fn ($query) => $query->orWhere('awb_code', $awb))
            ->when($orderId, fn ($query) => $query->orWhere('shiprocket_order_id', $orderId))
            ->when($channelOrderId, function ($query) use ($channelOrderId) {
                $query->orWhere('platform_order_id', $channelOrderId)
                    ->orWhere('fastrr_order_id', $channelOrderId)
                    ->orWhere('order_number', $channelOrderId);
            })
            ->first();
    }

    protected function mapShipmentStatus(?string $status): ?string
    {
        $status = Str::lower((string) $status);

        if ($status === '') {
            return null;
        }

        if (Str::contains($status, ['cancel', 'rto', 'return', 'undelivered', 'lost', 'destroyed', 'damaged'])) {
            return 'cancelled';
        }

        if (Str::contains($status, ['delivered'])) {
            return 'delivered';
        }

        if (Str::contains($status, ['shipped', 'in transit', 'transit', 'out for delivery', 'picked up'])) {
            return 'shipped';
        }

        if (Str::contains($status, ['ready', 'pickup', 'manifest', 'assigned', 'packed'])) {
            return 'processing';
        }

        return null;
    }

    protected function money($value): float
    {
        return round((float) ($value ?? 0), 2);
    }

    protected function dateOrNull($value): ?string
    {
        try {
            return $value ? Carbon::parse($value)->toDateString() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function dateTimeOrNull($value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
