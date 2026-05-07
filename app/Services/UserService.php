<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function createUser(UserDTO $dto)
    {
        if ($this->userRepository->findByEmail($dto->email)) {
            throw new Exception("Email already registered.");
        }

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'role' => $dto->role,
            'status' => $dto->status,
            'address' => $dto->address,
        ];

        if ($dto->password) {
            $data['password'] = Hash::make($dto->password);
        }

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, UserDTO $dto)
    {
        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'role' => $dto->role,
            'status' => $dto->status,
            'address' => $dto->address,
        ];

        if ($dto->password) {
            $data['password'] = Hash::make($dto->password);
        }

        return $this->userRepository->update($id, $data);
    }
}
