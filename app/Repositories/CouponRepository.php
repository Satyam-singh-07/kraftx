<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(Coupon $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->model->where('code', $code)->first();
    }

    public function getActiveCoupons()
    {
        $now = now();
        return $this->model->where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();
    }
}
