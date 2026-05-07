<?php

namespace App\Services;

use App\DTOs\CouponDTO;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Exception;

class CouponService
{
    public function __construct(
        protected CouponRepositoryInterface $couponRepository
    ) {}

    public function createCoupon(CouponDTO $dto)
    {
        if ($this->couponRepository->findByCode($dto->code)) {
            throw new Exception("Coupon code already exists.");
        }

        return $this->couponRepository->create([
            'code' => $dto->code,
            'discount_type' => $dto->discount_type,
            'discount_value' => $dto->discount_value,
            'min_cart_value' => $dto->min_cart_value,
            'max_discount' => $dto->max_discount,
            'usage_limit' => $dto->usage_limit,
            'start_date' => $dto->start_date,
            'end_date' => $dto->end_date,
            'status' => $dto->status,
        ]);
    }

    public function updateCoupon(int $id, CouponDTO $dto)
    {
        return $this->couponRepository->update($id, [
            'code' => $dto->code,
            'discount_type' => $dto->discount_type,
            'discount_value' => $dto->discount_value,
            'min_cart_value' => $dto->min_cart_value,
            'max_discount' => $dto->max_discount,
            'usage_limit' => $dto->usage_limit,
            'start_date' => $dto->start_date,
            'end_date' => $dto->end_date,
            'status' => $dto->status,
        ]);
    }
}
