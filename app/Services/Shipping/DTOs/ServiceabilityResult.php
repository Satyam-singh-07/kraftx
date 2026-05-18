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
        public readonly array $responseSnapshot = [],
        public readonly ?\DateTimeInterface $checkedAt = null,
        public readonly bool $fromCache = false,
        public readonly ?string $message = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'pincode' => $this->pincode,
            'is_serviceable' => $this->isServiceable,
            'cod_available' => $this->codAvailable,
            'prepaid_available' => $this->prepaidAvailable,
            'estimated_days' => $this->estimatedDays,
            'checked_at' => $this->checkedAt?->format(DATE_ATOM),
            'from_cache' => $this->fromCache,
            'message' => $this->message,
        ];
    }
}
