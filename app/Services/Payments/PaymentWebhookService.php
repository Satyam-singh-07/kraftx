<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Cart;
use App\Services\Orders\OrderConfirmationNotifier;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookService
{
    public function __construct(
        protected RazorpayService $razorpay,
        protected OrderConfirmationNotifier $confirmationNotifier
    ) {
    }

    public function handleRazorpayWebhook(string $rawPayload, ?string $signature): void
    {
        $this->razorpay->verifyWebhookSignature($rawPayload, $signature);

        $payload = json_decode($rawPayload, true, flags: JSON_THROW_ON_ERROR);
        $event = (string) ($payload['event'] ?? '');
        $payment = Arr::get($payload, 'payload.payment.entity', []);
        $razorpayOrderId = $payment['order_id'] ?? null;
        $paymentId = $payment['id'] ?? null;

        if (!$razorpayOrderId) {
            Log::warning('Razorpay webhook missing order id', ['event' => $event]);
            return;
        }

        $confirmedOrder = DB::transaction(function () use ($event, $payload, $razorpayOrderId, $paymentId) {
            $order = Order::where('payment_reference', $razorpayOrderId)->lockForUpdate()->first();

            if (!$order) {
                Log::warning('Razorpay webhook unmatched order', [
                    'event' => $event,
                    'payment_reference' => $razorpayOrderId,
                ]);
                return null;
            }

            $payments = $order->payments ?: [];
            $payments[] = [
                'provider' => 'razorpay',
                'event' => $event,
                'payment_id' => $paymentId,
                'received_at' => now()->toIso8601String(),
            ];

            if ($event === 'payment.captured' && $order->payment_status !== 'paid') {
                $order->fill([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $paymentId,
                    'paid_at' => now(),
                ]);

                if ($order->cart_id) {
                    $cart = Cart::whereKey((int) $order->cart_id)->lockForUpdate()->first();
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->update(['status' => 'converted']);
                    }
                }
            }

            if (in_array($event, ['payment.failed', 'order.payment_failed'], true) && $order->payment_status !== 'paid') {
                if ($order->payment_status !== 'failed') {
                    $this->releaseReservedStock($order);
                }

                $order->fill([
                    'status' => 'payment_failed',
                    'payment_status' => 'failed',
                ]);
            }

            $order->payment_payload = [
                'last_webhook' => [
                    'event' => $event,
                    'payment_id' => $paymentId,
                    'payload_id' => $payload['id'] ?? null,
                ],
            ];
            $order->payments = $payments;
            $order->save();

            return $event === 'payment.captured' && $order->payment_status === 'paid' ? $order : null;
        });

        if ($confirmedOrder) {
            $this->confirmationNotifier->send($confirmedOrder, 'razorpay_webhook');
        }
    }

    protected function releaseReservedStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                \App\Models\ProductVariant::whereKey($item->variant_id)->increment('stock', $item->quantity);
            } elseif ($item->product_id) {
                \App\Models\Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }
        }
    }
}
