<?php

namespace App\Services\Payments;

use App\Models\Order;
use Razorpay\Api\Api;

class RazorpayService implements PaymentGatewayInterface
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(
            (string) config('services.razorpay.key'),
            (string) config('services.razorpay.secret')
        );
    }

    public function createPaymentOrder(Order $order): array
    {
        $razorpayOrder = $this->api->order->create([
            'receipt' => $order->order_number,
            'amount' => (int) round($order->total_amount * 100),
            'currency' => 'INR',
            'payment_capture' => 1,
            'notes' => [
                'local_order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        return $razorpayOrder->toArray();
    }

    public function verifyPaymentSignature(array $payload): bool
    {
        $this->api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $payload['razorpay_order_id'] ?? '',
            'razorpay_payment_id' => $payload['razorpay_payment_id'] ?? '',
            'razorpay_signature' => $payload['razorpay_signature'] ?? '',
        ]);

        return true;
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (!$signature || !config('services.razorpay.webhook_secret')) {
            return false;
        }

        $this->api->utility->verifyWebhookSignature(
            $payload,
            $signature,
            (string) config('services.razorpay.webhook_secret')
        );

        return true;
    }
}
