<?php

namespace App\Services\Shipping\DTOs;

class ServiceabilityResult
{
    public function __construct(
        public readonly string $provider,
        public readonly string $pincode,
        public readonly bool $isServiceable,
        public readonly ?bool $codAvailable = null,
        public readonly ?bool $prepaidAvailable = null,
        public readonly ?int $estimatedDays = null,
        public readonly array $responseSnapshot = []
    ) {
    }
}
