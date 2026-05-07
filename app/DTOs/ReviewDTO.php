<?php

namespace App\DTOs;

class ReviewDTO
{
    public function __construct(
        public readonly int $product_id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly int $rating,
        public readonly string $comment,
        public readonly string $status,
        public readonly bool $show_on_home,
        public readonly array $images = []
    ) {}

    public static function fromRequest(array $data, array $images = []): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            name: $data['name'],
            email: $data['email'] ?? null,
            rating: (int) $data['rating'],
            comment: $data['comment'],
            status: $data['status'] ?? 'pending',
            show_on_home: isset($data['show_on_home']) ? (bool) $data['show_on_home'] : false,
            images: $images
        );
    }
}
