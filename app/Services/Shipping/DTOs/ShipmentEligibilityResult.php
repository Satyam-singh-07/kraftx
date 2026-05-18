<?php

namespace App\Services\Shipping\DTOs;

class ShipmentEligibilityResult
{
    public function __construct(
        public readonly bool $eligible,
        public readonly array $reasons = [],
        public readonly array $warnings = []
    ) {
    }

    public static function eligible(array $warnings = []): self
    {
        return new self(true, [], $warnings);
    }

    public static function blocked(array $reasons, array $warnings = []): self
    {
        return new self(false, $reasons, $warnings);
    }
}
