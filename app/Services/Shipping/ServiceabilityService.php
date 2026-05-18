<?php

namespace App\Services\Shipping;

use App\Models\ServiceabilityCheck;
use App\Services\Shipping\DTOs\ServiceabilityResult;

class ServiceabilityService
{
    public function cached(string $pincode, string $provider = 'delhivery'): ?ServiceabilityResult
    {
        $check = $this->cachedRecord($pincode, $provider);

        return $check ? $this->fromRecord($check, true) : null;
    }

    public function cachedRecord(string $pincode, string $provider = 'delhivery'): ?ServiceabilityCheck
    {
        $pincode = preg_replace('/\D+/', '', $pincode);

        return ServiceabilityCheck::where('provider', $provider)
            ->where('pincode', $pincode)
            ->first();
    }

    public function remember(ServiceabilityResult $result): ServiceabilityCheck
    {
        return ServiceabilityCheck::updateOrCreate(
            [
                'provider' => $result->provider,
                'pincode' => $result->pincode,
            ],
            [
                'is_serviceable' => $result->isServiceable,
                'cod_available' => $result->codAvailable,
                'prepaid_available' => $result->prepaidAvailable,
                'estimated_days' => $result->estimatedDays,
                'response_snapshot' => $result->responseSnapshot,
                'checked_at' => now(),
            ]
        );
    }

    public function isFresh(ServiceabilityCheck $check, int $ttlMinutes): bool
    {
        return $check->checked_at && $check->checked_at->greaterThanOrEqualTo(now()->subMinutes($ttlMinutes));
    }

    public function fromRecord(ServiceabilityCheck $check, bool $fromCache = true): ServiceabilityResult
    {
        return new ServiceabilityResult(
            provider: $check->provider,
            pincode: $check->pincode,
            isServiceable: $check->is_serviceable,
            codAvailable: $check->cod_available,
            prepaidAvailable: $check->prepaid_available,
            estimatedDays: $check->estimated_days,
            responseSnapshot: $check->response_snapshot ?: [],
            checkedAt: $check->checked_at,
            fromCache: $fromCache,
            message: $this->message($check->is_serviceable, $check->cod_available, $check->prepaid_available, $check->estimated_days)
        );
    }

    public function message(bool $serviceable, ?bool $codAvailable, ?bool $prepaidAvailable, ?int $estimatedDays): string
    {
        if (! $serviceable) {
            return 'Sorry, delivery is currently unavailable for this pincode.';
        }

        $parts = ['Delivery available.'];

        if ($codAvailable === false) {
            $parts[] = 'Cash on Delivery is not available for this location.';
        } elseif ($codAvailable === true) {
            $parts[] = 'COD available.';
        }

        if ($prepaidAvailable === true) {
            $parts[] = 'Prepaid delivery available.';
        }

        if ($estimatedDays) {
            $parts[] = 'Estimated delivery: '.$estimatedDays.' days.';
        }

        return implode(' ', $parts);
    }
}
