<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Shipping\DelhiveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ServiceabilityController extends Controller
{
    public function __invoke(Request $request, DelhiveryService $delhivery): JsonResponse
    {
        $key = 'serviceability:'.sha1($request->ip().'|'.$request->session()->getId());
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return response()->json([
                'ok' => false,
                'message' => 'Please wait a moment before checking another pincode.',
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'payment_mode' => ['nullable', 'string', 'in:cod,razorpay,prepaid'],
        ]);

        try {
            $result = $delhivery->checkServiceability(
                $validated['pincode'],
                $validated['payment_mode'] ?? null
            );

            return response()->json([
                'ok' => true,
                'serviceability' => $result->toArray(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Checkout serviceability check unavailable', [
                'pincode' => $validated['pincode'],
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'We could not check delivery availability right now. You can continue checkout and our team will verify it.',
            ], 200);
        }
    }
}
