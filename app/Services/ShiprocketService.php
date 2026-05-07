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
                'X-Api-Key' => $apiKey,
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
}
