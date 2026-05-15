<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('orders:link-guests', function () {
    $linked = app(\App\Services\OrderLinkingService::class)->linkAllGuestOrders();

    $this->info("Linked {$linked} guest order(s).");
    return self::SUCCESS;
})->purpose('Link guest orders to customer accounts by email or phone');
