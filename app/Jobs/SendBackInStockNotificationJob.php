<?php

namespace App\Jobs;

use App\Models\ProductNotifyRequest;
use App\Notifications\BackInStockNotification;
use App\Services\ProductDemandService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendBackInStockNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $productNotifyRequestId)
    {
    }

    public function handle(ProductDemandService $productDemandService): void
    {
        $notifyRequest = ProductNotifyRequest::with(['product.images', 'user'])
            ->find($this->productNotifyRequestId);

        if (!$notifyRequest || $notifyRequest->is_notified || !$notifyRequest->user || !$notifyRequest->product) {
            Log::info('Back in stock notification job skipped', [
                'notify_request_id' => $this->productNotifyRequestId,
                'reason' => !$notifyRequest ? 'missing_request' : 'already_notified_or_missing_relation',
            ]);

            return;
        }

        if ($notifyRequest->product->stock <= 0) {
            Log::info('Back in stock notification job skipped', [
                'notify_request_id' => $notifyRequest->id,
                'product_id' => $notifyRequest->product_id,
                'reason' => 'product_not_in_stock',
                'stock' => $notifyRequest->product->stock,
            ]);

            return;
        }

        if (!filter_var($notifyRequest->user->email, FILTER_VALIDATE_EMAIL)) {
            Log::error('Back in stock notification email failed', [
                'notify_request_id' => $notifyRequest->id,
                'product_id' => $notifyRequest->product_id,
                'user_id' => $notifyRequest->user_id,
                'user_email' => $notifyRequest->user->email,
                'message' => 'Invalid recipient email address.',
            ]);

            return;
        }

        Log::info('Back in stock notification email send started', [
            'notify_request_id' => $notifyRequest->id,
            'product_id' => $notifyRequest->product_id,
            'user_id' => $notifyRequest->user_id,
            'user_email' => $notifyRequest->user->email,
            'mail_mailer' => config('mail.default'),
        ]);

        try {
            $notifyRequest->user->notify(new BackInStockNotification($notifyRequest->product));
            $productDemandService->markRequestNotified($notifyRequest);

            Log::info('Back in stock notification email sent', [
                'notify_request_id' => $notifyRequest->id,
                'product_id' => $notifyRequest->product_id,
                'user_id' => $notifyRequest->user_id,
                'user_email' => $notifyRequest->user->email,
            ]);
        } catch (Throwable $e) {
            Log::error('Back in stock notification email failed', [
                'notify_request_id' => $notifyRequest->id,
                'product_id' => $notifyRequest->product_id,
                'user_id' => $notifyRequest->user_id,
                'user_email' => $notifyRequest->user->email,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('Back in stock notification job permanently failed', [
            'notify_request_id' => $this->productNotifyRequestId,
            'exception' => $e::class,
            'message' => $e->getMessage(),
        ]);
    }
}
