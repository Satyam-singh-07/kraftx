<?php

namespace App\Services\Orders;

use App\Mail\OrderAdminNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderConfirmationNotifier
{
    private const ADMIN_EMAILS = [
        'sanjayadav448@gmail.com',
        'swatidayal2004@gmail.com',
        'satyamsingh962572@gmail.com'        
        // Add more admin emails here:
        // 'admin@example.com',
    ];

    public function send(Order $order, string $trigger): void
    {
        if (!$this->shouldSend($order)) {
            Log::info('Order confirmation email skipped', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
                'reason' => $this->skipReason($order),
            ]);

            return;
        }

        Log::info('Order confirmation email dispatch start', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'trigger' => $trigger,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
        ]);

        try {
            $updated = Order::whereKey($order->id)
                ->whereNull('confirmation_email_sent_at')
                ->update(['confirmation_email_sent_at' => now()]);

            if ($updated !== 1) {
                Log::info('Order confirmation email skipped', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'trigger' => $trigger,
                    'reason' => 'already_marked_sent',
                ]);

                return;
            }

            $order = Order::with(['items.product.images', 'items.linkedProduct.images', 'items.variant'])->findOrFail($order->id);

            Mail::to($order->customer_email, $order->customer_name)
                ->sendNow(new OrderConfirmationMail($order));

            $this->sendAdminNotification($order, $trigger);

            Log::info('Order confirmation email sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
            ]);
        } catch (Throwable $e) {
            Order::whereKey($order->id)->update(['confirmation_email_sent_at' => null]);

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
        return $this->skipReason($order) === null;
    }

    protected function skipReason(Order $order): ?string
    {
        if (! filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            return 'invalid_customer_email';
        }

        if ($order->confirmation_email_sent_at) {
            return 'already_sent';
        }

        if ($order->payment_method === 'COD' && $order->status === 'cod_confirmed') {
            return null;
        }

        if ($order->payment_status === 'paid') {
            return null;
        }

        return 'payment_not_confirmed';
    }

    protected function sendAdminNotification(Order $order, string $trigger): void
    {
        $adminEmails = $this->adminEmails();

        if ($adminEmails === []) {
            Log::info('Admin order notification skipped', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
                'reason' => 'no_admin_emails_configured',
            ]);

            return;
        }

        try {
            Mail::to($adminEmails)->sendNow(new OrderAdminNotificationMail($order));

            Log::info('Admin order notification sent', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
                'recipient_count' => count($adminEmails),
            ]);
        } catch (Throwable $e) {
            Log::error('Admin order notification failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'trigger' => $trigger,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function adminEmails(): array
    {
        return array_values(array_unique(array_filter(
            array_map('trim', self::ADMIN_EMAILS),
            fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        )));
    }
}
