<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;

class PaymentStrategy
{
    public const CACHE_KEY = 'store.payment_strategy';
    public const SETTING_KEY = 'payment_strategy';

    public function settings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $stored = StoreSetting::where('key', self::SETTING_KEY)->first()?->value ?: [];

            return array_merge($this->defaults(), $stored);
        });
    }

    public function save(array $settings): array
    {
        $settings = array_merge($this->defaults(), $settings);

        StoreSetting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => $settings]
        );

        Cache::forget(self::CACHE_KEY);

        return $settings;
    }

    public function defaults(): array
    {
        return [
            'cod_enabled' => true,
            'cod_fee_enabled' => false,
            'cod_fee_amount' => 0.0,
            'cod_free_above' => 0.0,
            'prepaid_discount_enabled' => false,
            'prepaid_discount_amount' => 0.0,
            'prepaid_free_shipping' => false,
        ];
    }

    public function totals(float $subtotal, string $paymentMethod, ?array $settings = null, float $baseShipping = 0): array
    {
        $settings ??= $this->settings();
        $paymentFee = 0.0;
        $paymentDiscount = 0.0;
        $shipping = $baseShipping;

        if ($paymentMethod === 'cod' && $settings['cod_fee_enabled']) {
            $freeAbove = (float) $settings['cod_free_above'];
            $paymentFee = $freeAbove > 0 && $subtotal >= $freeAbove
                ? 0.0
                : (float) $settings['cod_fee_amount'];
        }

        if ($paymentMethod === 'razorpay') {
            if ($settings['prepaid_discount_enabled']) {
                $paymentDiscount = min((float) $settings['prepaid_discount_amount'], $subtotal);
            }

            if ($settings['prepaid_free_shipping']) {
                $shipping = 0.0;
            }
        }

        $total = max(0, round($subtotal + $shipping + $paymentFee - $paymentDiscount, 2));

        return [
            'subtotal' => round($subtotal, 2),
            'shipping_amount' => round($shipping, 2),
            'payment_fee_amount' => round($paymentFee, 2),
            'payment_discount_amount' => round($paymentDiscount, 2),
            'total_amount' => $total,
        ];
    }
}
