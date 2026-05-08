<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShiprocketService
{
    protected $baseUrl = 'https://apiv2.shiprocket.in/v1/external';
    protected $email;
    protected $password;

    public function __construct()
    {
        $this->email = config('services.shiprocket.email');
        $this->password = config('services.shiprocket.password');
    }

    /**
     * Authenticate with Shiprocket and get a JWT token.
     */
    public function getToken()
    {
        return Cache::remember('shiprocket_token', 86400, function () {
            try {
                $response = Http::post("{$this->baseUrl}/auth/login", [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

                if ($response->successful()) {
                    return $response->json('token');
                }

                Log::error('Shiprocket Login Failed:', $response->json());
                return null;
            } catch (\Exception $e) {
                Log::error('Shiprocket Login Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get orders from Shiprocket.
     */
    public function getOrders($params = [])
    {
        $token = $this->getToken();
        if (!$token) return null;

        $response = Http::withToken($token)->get("{$this->baseUrl}/orders", $params);
        return $response->json();
    }

    /**
     * Get specific order details from Shiprocket.
     */
    public function getOrderDetails($orderId)
    {
        $token = $this->getToken();
        if (!$token) return null;

        $response = Http::withToken($token)->get("{$this->baseUrl}/orders/show/{$orderId}");
        return $response->json();
    }

    /**
     * Create a new custom order in Shiprocket (Logistics API).
     */
    public function createOrder($orderData)
    {
        $token = $this->getToken();
        if (!$token) return null;

        $response = Http::withToken($token)->post("{$this->baseUrl}/orders/create/adhoc", $orderData);
        
        if (!$response->successful()) {
            Log::error('Shiprocket Order Creation Failed:', $response->json());
        }

        return $response->json();
    }

    /**
     * Generate a checkout token for Shiprocket One-Click Checkout.
     */
    public function getCheckoutToken($cartData)
    {
        $timestamp = now()->toIso8601String();
        $apiKey = config('services.shiprocket.key');
        $apiSecret = config('services.shiprocket.secret');

        $payload = [
            'cart_data' => $cartData,
            'redirect_url' => url('/account/orders'),
            'timestamp' => $timestamp,
        ];

        $body = json_encode($payload);
        $hmac = base64_encode(hash_hmac('sha256', $body, $apiSecret, true));

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => 'Bearer ' . $apiKey,
                'X-Api-HMAC-SHA256' => $hmac,
                'Content-Type' => 'application/json',
            ])->post('https://checkout-api.shiprocket.com/api/v1/access-token/checkout', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => true,
                'status' => $response->status(),
                'message' => $response->json('message') ?? 'Unknown error',
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get order details from the Shiprocket Checkout (Fastrr) API.
     */
    public function getCheckoutOrderDetails($orderId)
    {
        $timestamp = now()->toIso8601String();
        $apiKey = config('services.shiprocket.key');
        $apiSecret = config('services.shiprocket.secret');

        $payload = [
            'order_id' => $orderId,
            'timestamp' => $timestamp,
        ];

        $body = json_encode($payload);
        $hmac = base64_encode(hash_hmac('sha256', $body, $apiSecret, true));

        try {
            $url = 'https://checkout-api.shiprocket.com/api/v1/custom-platform-order/details';
            
            $response = Http::withHeaders([
                'X-Api-Key' => 'Bearer ' . $apiKey,
                'X-Api-HMAC-SHA256' => $hmac,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Shiprocket Checkout Order Details Failed:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Shiprocket Checkout Order Details Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync a product to Shiprocket Checkout.
     */
    public function syncProduct($product)
    {
        $apiKey = config('services.shiprocket.key');
        $apiSecret = config('services.shiprocket.secret');

        $primaryImage = $product->primary_image ? asset('storage/' . $product->primary_image->image_path) : "";

        $payload = [
            'id' => (int) $product->id,
            'title' => (string) ($product->name ?? ""),
            'body_html' => (string) ($product->description ?? ""),
            'vendor' => config('app.name', 'KraftX'),
            'product_type' => (string) ($product->collections->first()?->name ?? "Handicrafts"),
            'updated_at' => $product->updated_at->toIso8601String(),
            'status' => $product->status ? 'active' : 'archived',
            'variants' => $product->variants->map(function ($variant) use ($product, $primaryImage) {
                return [
                    'id' => (int) $variant->id,
                    'title' => (string) (($variant->color ? $variant->color : "") . ($variant->size ? " / " . $variant->size : ($variant->color ? "" : "Default Title"))),
                    'price' => number_format($variant->price ?? $product->sale_price ?? $product->price, 2, ".", ""),
                    'quantity' => (int) $variant->stock,
                    'sku' => (string) ($variant->sku ?? $product->sku ?? ""),
                    'updated_at' => $variant->updated_at->toIso8601String(),
                    'image' => [
                        'src' => $primaryImage
                    ],
                    'weight' => (float) $product->weight,
                ];
            })->toArray(),
            'image' => [
                'src' => $primaryImage
            ]
        ];

        // If no variants, add default
        if (empty($payload['variants'])) {
            $payload['variants'][] = [
                'id' => (int) ($product->id + 900000000),
                'title' => "Default Title",
                'price' => number_format($product->sale_price ?? $product->price, 2, ".", ""),
                'quantity' => (int) $product->stock,
                'sku' => (string) ($product->sku ?? ""),
                'updated_at' => $product->updated_at->toIso8601String(),
                'image' => [
                    'src' => $primaryImage
                ],
                'weight' => (float) $product->weight,
            ];
        }

        $body = json_encode($payload);
        $hmac = base64_encode(hash_hmac('sha256', $body, $apiSecret, true));

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => 'Bearer ' . $apiKey,
                'X-Api-HMAC-SHA256' => $hmac,
                'Content-Type' => 'application/json',
            ])->post('https://checkout-api.shiprocket.com/wh/v1/custom/product', $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Shiprocket Product Sync Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync a collection to Shiprocket Checkout.
     */
    public function syncCollection($collection)
    {
        $apiKey = config('services.shiprocket.key');
        $apiSecret = config('services.shiprocket.secret');

        $payload = [
            'id' => (int) $collection->id,
            'updated_at' => $collection->updated_at->toIso8601String(),
            'title' => (string) ($collection->name ?? ""),
            'body_html' => (string) ($collection->description ?? ""),
            'image' => [
                'src' => $collection->image ? asset('storage/' . $collection->image) : "",
            ]
        ];

        $body = json_encode($payload);
        $hmac = base64_encode(hash_hmac('sha256', $body, $apiSecret, true));

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => 'Bearer ' . $apiKey,
                'X-Api-HMAC-SHA256' => $hmac,
                'Content-Type' => 'application/json',
            ])->post('https://checkout-api.shiprocket.com/wh/v1/custom/collection', $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Shiprocket Collection Sync Exception: ' . $e->getMessage());
            return false;
        }
    }
}
