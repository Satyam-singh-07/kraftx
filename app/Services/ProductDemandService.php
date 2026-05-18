<?php

namespace App\Services;

use App\Jobs\SendBackInStockNotificationJob;
use App\Models\Product;
use App\Models\ProductNotifyRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductDemandService
{
    public function createRequest(Product $product, User $user): ProductNotifyRequest
    {
        if ($product->stock > 0) {
            throw ValidationException::withMessages([
                'product_id' => 'This product is already in stock.',
            ]);
        }

        $existing = ProductNotifyRequest::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'product_id' => 'You have already requested a notification for this product.',
            ]);
        }

        $notifyRequest = ProductNotifyRequest::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'is_notified' => false,
        ]);

        Log::info('Product notify request created', [
            'notify_request_id' => $notifyRequest->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        return $notifyRequest;
    }

    public function handleStockTransition(Product $product, int $oldStock, int $newStock): int
    {
        if ($oldStock > 0 || $newStock <= 0) {
            return 0;
        }

        $requestIds = $product->notifyRequests()
            ->where('is_notified', false)
            ->pluck('id');

        Log::info('Back in stock notification dispatch started', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'pending_request_count' => $requestIds->count(),
            'queue_connection' => config('queue.default'),
            'queue' => config('product_demand.queue', 'default'),
            'mail_mailer' => config('mail.default'),
        ]);

        $requestIds->each(
            function (int $requestId) use ($product): void {
                SendBackInStockNotificationJob::dispatch($requestId)
                    ->onQueue(config('product_demand.queue', 'default'));

                Log::info('Back in stock notification email queued', [
                    'notify_request_id' => $requestId,
                    'product_id' => $product->id,
                    'queue_connection' => config('queue.default'),
                    'queue' => config('product_demand.queue', 'default'),
                ]);
            }
        );

        return $requestIds->count();
    }

    public function demandQuery(array $filters = []): Builder
    {
        return Product::query()
            ->with(['images'])
            ->withCount([
                'notifyRequests as notify_requests_count' => fn (Builder $query) => $this->applyRequestFilters($query, $filters),
            ])
            ->whereHas('notifyRequests', fn (Builder $query) => $this->applyRequestFilters($query, $filters));
    }

    public function requestQueryForProduct(Product $product, array $filters = []): Builder
    {
        $query = ProductNotifyRequest::query()
            ->with('user')
            ->where('product_id', $product->id);

        return $this->applyRequestFilters($query, $filters)->latest();
    }

    public function dashboardStats(): array
    {
        return [
            'most_requested_products' => $this->topRequestedProducts(5),
            'total_pending_notifications' => ProductNotifyRequest::where('is_notified', false)->count(),
            'total_notified_users' => ProductNotifyRequest::where('is_notified', true)->count(),
            'total_waiting_users' => ProductNotifyRequest::where('is_notified', false)->distinct('user_id')->count('user_id'),
            'recent_requests' => ProductNotifyRequest::with(['product.images', 'user'])
                ->latest()
                ->limit(6)
                ->get(),
        ];
    }

    public function topRequestedProducts(int $limit = 10): Collection
    {
        return Product::with('images')
            ->withCount('notifyRequests')
            ->having('notify_requests_count', '>', 0)
            ->orderByDesc('notify_requests_count')
            ->limit($limit)
            ->get();
    }

    public function productsWithPendingDemand(): Collection
    {
        return Product::with('images')
            ->withCount(['notifyRequests as pending_notify_requests_count' => fn (Builder $query) => $query->where('is_notified', false)])
            ->having('pending_notify_requests_count', '>', 0)
            ->orderByDesc('pending_notify_requests_count')
            ->get();
    }

    public function usersWaitingForProduct(Product $product): Collection
    {
        return $product->notifyRequests()
            ->with('user')
            ->where('is_notified', false)
            ->latest()
            ->get()
            ->pluck('user')
            ->filter();
    }

    public function requestsForUser(User $user, int $perPage = 12)
    {
        return ProductNotifyRequest::with(['product.images'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function markRequestNotified(ProductNotifyRequest $notifyRequest): void
    {
        DB::transaction(function () use ($notifyRequest): void {
            $notifyRequest->refresh();

            if (!$notifyRequest->is_notified) {
                $notifyRequest->forceFill(['is_notified' => true])->save();
            }
        });
    }

    private function applyRequestFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(($filters['notified'] ?? '') !== '', function (Builder $query) use ($filters) {
                $query->where('is_notified', (bool) $filters['notified']);
            })
            ->when(!empty($filters['date']), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', $filters['date']);
            });
    }
}
