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

        $shippingAddress = is_array($orderData['shipping_address'] ?? null) ? $orderData['shipping_address'] : [];
        $email = $shippingAddress['email'] ?? $orderData['email'] ?? $orderData['customer_email'] ?? 'N/A';
        $phone = $shippingAddress['phone'] ?? $orderData['phone'] ?? $orderData['customer_phone'] ?? 'N/A';
        $name = $this->customerName($shippingAddress, $orderData);

        $user = null;
        if ($email !== 'N/A') {
            $user = User::where('email', $email)->first();
        }

        DB::beginTransaction();
        try {
            $order = Order::where('shiprocket_order_id', $shiprocketOrderId)->lockForUpdate()->first();
            $isNewOrder = !$order;

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
                'shipping_address_data' => is_array($shippingAddress) ? $shippingAddress : null,
                'billing_address_data' => is_array($orderData['billing_address'] ?? null) ? $orderData['billing_address'] : null,
                'payments' => $orderData['payments'] ?? [],
                'coupon_codes' => $orderData['coupon_codes'] ?? [],
                'discount_detail' => $orderData['discount_detail'] ?? null,
                'shiprocket_tags' => $orderData['tags'] ?? [],
                'shiprocket_payload' => $orderData,
            ]);
            $order->save();

            $shouldCreateItems = $isNewOrder || !$order->items()->exists();
            if ($shouldCreateItems) {
                $order->items()->delete();
                $this->createItems($order, $orderData, $shouldCreateItems);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNewOrder ? 'Order created' : 'Order updated',
                'order_id' => $order->id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shiprocket Checkout Order Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $orderData,
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    protected function verifyRequest(Request $request): bool
    {
        return $this->verifySecurityToken($request) && $this->verifySignature($request);
    }

    protected function verifySecurityToken(Request $request): bool
    {
        $expectedToken = config('services.shiprocket.webhook_token');

        if (!$expectedToken) {
            return true;
        }

        $providedToken = $request->header('x-api-key');

        return $providedToken && hash_equals($expectedToken, $providedToken);
    }

    protected function verifySignature(Request $request): bool
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
