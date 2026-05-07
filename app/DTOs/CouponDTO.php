<?php

namespace App\DTOs;

class CouponDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $discount_type,
        public readonly float $discount_value,
        public readonly float $min_cart_value,
        public readonly ?float $max_discount,
        public readonly ?int $usage_limit,
        public readonly ?string $start_date,
        public readonly ?string $end_date,
        public readonly bool $status
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'],
            discount_type: $data['discount_type'],
            discount_value: (float) $data['discount_value'],
            min_cart_value: (float) ($data['min_cart_value'] ?? 0),
            max_discount: isset($data['max_discount']) ? (float) $data['max_discount'] : null,
            usage_limit: isset($data['usage_limit']) ? (int) $data['usage_limit'] : null,
            start_date: $data['start_date'] ?? null,
            end_date: $data['end_date'] ?? null,
            status: isset($data['status']) ? (bool) $data['status'] : true
        );
    }
}
