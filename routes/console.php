<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('shiprocket:import-order {order_id}', function (string $orderId) {
    $details = app(\App\Services\ShiprocketService::class)->getCheckoutOrderDetails($orderId);

    $candidates = [
        Arr::get($details, 'data.order'),
        Arr::get($details, 'data'),
        Arr::get($details, 'result.order'),
        Arr::get($details, 'result'),
        $details,
    ];

    $orderData = null;
    foreach ($candidates as $candidate) {
        if (is_array($candidate) && (isset($candidate['order_id']) || isset($candidate['platform_order_id']) || isset($candidate['fastrr_order_id']))) {
            $orderData = $candidate;
            break;
        }
    }

    if (!$orderData) {
        $this->error('Could not fetch order details from Shiprocket.');
        $this->line(json_encode($details, JSON_PRETTY_PRINT));
        return self::FAILURE;
    }

    $orderData['order_id'] = $orderData['order_id'] ?? $orderId;
    $orderData['status'] = $orderData['status'] ?? 'SUCCESS';

    $result = app(\App\Http\Controllers\Api\ShiprocketWebhookController::class)->storeCheckoutOrder($orderData);

    $this->info(($result['created'] ? 'Created' : 'Updated') . ' local order ID: ' . $result['order']->id);
    return self::SUCCESS;
})->purpose('Import a Shiprocket Checkout order by order_id');

Artisan::command('orders:link-guests', function () {
    $linked = app(\App\Services\OrderLinkingService::class)->linkAllGuestOrders();

    $this->info("Linked {$linked} guest order(s).");
    return self::SUCCESS;
})->purpose('Link guest orders to customer accounts by email or phone');
