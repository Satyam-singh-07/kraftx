<?php

namespace App\Services\Shipping;

use App\Models\ServiceabilityCheck;
use App\Services\Shipping\DTOs\ServiceabilityResult;

class ServiceabilityService
{
    public function cached(string $pincode, string $provider = 'delhivery'): ?ServiceabilityResult
    {
        $pincode = preg_replace('/\D+/', '', $pincode);

        $check = ServiceabilityCheck::where('provider', $provider)
            ->where('pincode', $pincode)
            ->first();

        if (! $check) {
            return null;
        }

        return new ServiceabilityResult(
            provider: $check->provider,
            pincode: $check->pincode,
            isServiceable: $check->is_serviceable,
            codAvailable: $check->cod_available,
            prepaidAvailable: $check->prepaid_available,
            estimatedDays: $check->estimated_days,
            responseSnapshot: $check->response_snapshot ?: []
        );
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
}
