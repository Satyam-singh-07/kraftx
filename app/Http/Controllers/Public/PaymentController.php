<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\PaymentVerificationService;
use App\Services\Payments\PaymentWebhookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function verify(Request $request, Order $order, PaymentVerificationService $verification): RedirectResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        try {
            $order = $verification->verifyRazorpayCallback($order, $validated);
            session(['last_order_id' => $order->id]);

            return redirect()->route('checkout.success', $order)->with('success', 'Payment verified successfully.');
        } catch (\Throwable $e) {
            Log::warning('Payment verification failed', [
                'order_id' => $order->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('checkout.payment', $order)->with('error', 'Payment verification failed. Please retry payment or contact support.');
        }
    }

    public function webhook(Request $request, PaymentWebhookService $webhooks)
    {
        try {
            $webhooks->handleRazorpayWebhook(
                $request->getContent(),
                $request->header('X-Razorpay-Signature')
            );
        } catch (\Throwable $e) {
            Log::warning('Razorpay webhook rejected', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid webhook'], 400);
        }

        return response()->json(['status' => 'ok']);
    }
}
