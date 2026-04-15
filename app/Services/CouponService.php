<?php

namespace App\Services;

use App\Models\Coupon;
use App\Exceptions\InvalidCouponException;

class CouponService
{
    public function validateAndApply(string $code, float $cartTotal)
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid($cartTotal)) {
            throw new InvalidCouponException();
        }

        return [
            'valid' => true,
            'discount_amount' => $coupon->calculateDiscount($cartTotal),
            'coupon_id' => $coupon->id,
            'code' => $coupon->code
        ];
    }
    
    public function incrementUsage(Coupon $coupon)
    {
        $coupon->increment('used_count');
    }
}
