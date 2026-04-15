<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\Request;
use App\Exceptions\InvalidCouponException;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'cart_total' => 'required|numeric|min:0',
        ]);

        try {
            $result = $this->couponService->validateAndApply($request->code, $request->cart_total);
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'data' => $result
            ]);
        } catch (InvalidCouponException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
