<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\ShipmentAttempt;

class ShipmentAttemptService
{
    public function prepare(Order $order, string $provider, string $action = 'create_shipment'): ShipmentAttempt
    {
        return ShipmentAttempt::firstOrCreate(
            [
                'provider' => $provider,
                'idempotency_key' => $this->key($order, $provider, $action),
            ],
            [
                'order_id' => $order->id,
                'action' => $action,
                'status' => 'pending',
                'metadata' => [
                    'order_number' => $order->order_number,
                    'prepared_at' => now()->toIso8601String(),
                ],
            ]
        );
    }

    public function key(Order $order, string $provider, string $action): string
    {
        return implode(':', [
            $provider,
            $action,
            $order->order_number,
        ]);
    }
}
