<?php

namespace App\Services\Orders;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderConfirmationNotifier
{
    public function send(Order $order, string $trigger): void
    {
        if (!$this->shouldSend($order)) {
            return;
        }

        try {
            $updated = Order::whereKey($order->id)
                ->whereNull('confirmation_email_sent_at')
                ->update(['confirmation_email_sent_at' => now()]);

            if ($updated !== 1) {
                return;
            }

            $order = Order::with(['items.product.images'])->findOrFail($order->id);

            Mail::to($order->customer_email, $order->customer_name)
                ->queue(new OrderConfirmationMail($order));

            Log::info('Order confirmation email queued', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
            ]);
        } catch (Throwable $e) {
            Log::error('Order confirmation email dispatch failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldSend(Order $order): bool
    {
        if (!filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($order->confirmation_email_sent_at) {
            return false;
        }

        if ($order->payment_method === 'COD' && $order->status === 'cod_confirmed') {
            return true;
        }

        return $order->payment_status === 'paid';
    }
}
