<?php

namespace App\Policies;

use App\Models\ProductNotifyRequest;
use App\Models\User;

class ProductNotifyRequestPolicy
{
    public function delete(User $user, ProductNotifyRequest $productNotifyRequest): bool
    {
        return $productNotifyRequest->user_id === $user->id;
    }
}
