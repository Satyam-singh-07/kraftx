<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $password,
        public readonly string $role,
        public readonly bool $status,
        public readonly ?string $address
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            password: $data['password'] ?? null,
            role: $data['role'] ?? 'customer',
            status: isset($data['status']) ? (bool) $data['status'] : true,
            address: $data['address'] ?? null
        );
    }
}
