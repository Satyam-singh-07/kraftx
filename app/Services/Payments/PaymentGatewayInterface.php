<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createPaymentOrder(Order $order): array;

    public function verifyPaymentSignature(array $payload): bool;

    public function verifyWebhookSignature(string $payload, ?string $signature): bool;
}
