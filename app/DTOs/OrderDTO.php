<?php

namespace App\DTOs;

class OrderDTO
{
    public function __construct(
        public readonly ?int $user_id,
        public readonly float $total_amount,
        public readonly float $subtotal,
        public readonly float $tax_amount,
        public readonly float $shipping_amount,
        public readonly float $discount_amount,
        public readonly string $status,
        public readonly ?string $payment_method,
        public readonly string $payment_status,
        public readonly string $customer_name,
        public readonly string $customer_email,
        public readonly string $customer_phone,
        public readonly string $shipping_address,
        public readonly string $shipping_city,
        public readonly string $shipping_state,
        public readonly string $shipping_pincode,
        public readonly string $shipping_country,
        public readonly ?string $notes,
        public readonly array $items = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            user_id: $data['user_id'] ?? null,
            total_amount: (float) $data['total_amount'],
            subtotal: (float) $data['subtotal'],
            tax_amount: (float) ($data['tax_amount'] ?? 0),
            shipping_amount: (float) ($data['shipping_amount'] ?? 0),
            discount_amount: (float) ($data['discount_amount'] ?? 0),
            status: $data['status'] ?? 'pending',
            payment_method: $data['payment_method'] ?? null,
            payment_status: $data['payment_status'] ?? 'pending',
            customer_name: $data['customer_name'],
            customer_email: $data['customer_email'],
            customer_phone: $data['customer_phone'],
            shipping_address: $data['shipping_address'],
            shipping_city: $data['shipping_city'],
            shipping_state: $data['shipping_state'],
            shipping_pincode: $data['shipping_pincode'],
            shipping_country: $data['shipping_country'] ?? 'India',
            notes: $data['notes'] ?? null,
            items: $data['items'] ?? []
        );
    }
}
