<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentVerificationService
{
    public function __construct(
        protected RazorpayService $razorpay
    ) {
    }

    public function verifyRazorpayCallback(Order $order, array $payload): Order
    {
        return DB::transaction(function () use ($order, $payload) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->payment_status === 'paid') {
                return $order;
            }

            $this->razorpay->verifyPaymentSignature($payload);

            if ($order->payment_reference !== ($payload['razorpay_order_id'] ?? null)) {
                Log::warning('Payment callback reference mismatch', [
                    'order_id' => $order->id,
                    'expected_reference' => $order->payment_reference,
                    'provided_reference' => $payload['razorpay_order_id'] ?? null,
                ]);

                throw new \RuntimeException('Payment reference mismatch.');
            }

            $payments = $order->payments ?: [];
            $payments[] = [
                'provider' => 'razorpay',
                'event' => 'callback_verified',
                'payment_id' => $payload['razorpay_payment_id'] ?? null,
                'verified_at' => now()->toIso8601String(),
            ];

            $order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_transaction_id' => $payload['razorpay_payment_id'] ?? null,
                'payment_payload' => [
                    'callback' => [
                        'razorpay_order_id' => $payload['razorpay_order_id'] ?? null,
                        'razorpay_payment_id' => $payload['razorpay_payment_id'] ?? null,
                    ],
                ],
                'payments' => $payments,
                'paid_at' => now(),
            ]);

            if ($order->cart_id) {
                $cart = Cart::whereKey((int) $order->cart_id)->lockForUpdate()->first();
                if ($cart) {
                    $cart->items()->delete();
                    $cart->update(['status' => 'converted']);
                }
            }

            return $order;
        });
    }
}
