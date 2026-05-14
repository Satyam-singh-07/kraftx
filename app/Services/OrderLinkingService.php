<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderLinkingService
{
    public function linkGuestOrders(User $user): int
    {
        $email = $this->normalizeEmail($user->email);
        $phone = $this->normalizePhone($user->phone);

        if (!$email && !$phone) {
            return 0;
        }

        return DB::transaction(function () use ($user, $email, $phone) {
            $query = Order::whereNull('user_id')
                ->where(function (Builder $query) use ($email, $phone) {
                    if ($email) {
                        $query->orWhereRaw('LOWER(customer_email) = ?', [$email]);
                    }

                    if ($phone) {
                        $query->orWhere('customer_phone', $phone);
                    }
                });

            $matchedOrders = $query->lockForUpdate()->get();

            if ($matchedOrders->isEmpty()) {
                return 0;
            }

            $matchedOrders->each->update(['user_id' => $user->id]);

            if (!$user->phone) {
                $phoneFromOrder = $matchedOrders
                    ->map(fn (Order $order) => $this->normalizePhone($order->customer_phone))
                    ->first();

                if ($phoneFromOrder) {
                    $user->forceFill(['phone' => $phoneFromOrder])->save();
                }
            }

            return $matchedOrders->count();
        });
    }

    public function linkAllGuestOrders(): int
    {
        $linked = 0;

        User::where('role', 'customer')->chunkById(100, function ($users) use (&$linked) {
            foreach ($users as $user) {
                $linked += $this->linkGuestOrders($user);
            }
        });

        return $linked;
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        $email = $this->normalizeEmail($user->email);
        $phone = $this->normalizePhone($user->phone);

        return $query->where(function (Builder $query) use ($user, $email, $phone) {
            $query->where('user_id', $user->id);

            if ($email) {
                $query->orWhereRaw('LOWER(customer_email) = ?', [$email]);
            }

            if ($phone) {
                $query->orWhere('customer_phone', $phone);
            }
        });
    }

    protected function normalizeEmail(?string $email): ?string
    {
        $email = trim(strtolower((string) $email));

        return $email !== '' ? $email : null;
    }

    protected function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);

        return $phone !== '' ? $phone : null;
    }
}
