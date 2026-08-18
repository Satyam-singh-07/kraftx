<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkOrderInquiryRequest;
use App\Mail\BulkOrderAdminNotificationMail;
use App\Mail\BulkOrderCustomerConfirmationMail;
use App\Models\BulkOrderInquiry;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BulkOrderInquiryController extends Controller
{
    public function requestOtp(BulkOrderInquiryRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->status, 404);

        $draft = $request->validated();
        $draft['name'] = trim($draft['name']);
        $draft['phone'] = trim($draft['phone']);
        $draft['email'] = strtolower(trim($draft['email']));
        $draft['message'] = isset($draft['message']) && $draft['message'] !== null
            ? trim($draft['message'])
            : null;

        return $this->sendVerificationOtp($request, $product, $draft);
    }

    public function resendOtp(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status, 404);

        $pending = $request->session()->get('bulk_order_otp');
        if (! is_array($pending) || ($pending['product_id'] ?? null) !== $product->id || ! is_array($pending['draft'] ?? null)) {
            return back()->withErrors(['email' => 'Please submit the bulk order form again to request an OTP.']);
        }

        return $this->sendVerificationOtp($request, $product, $pending['draft']);
    }

    public function verifyOtp(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status, 404);

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);
        $pending = $request->session()->get('bulk_order_otp');

        if (! is_array($pending) || ($pending['product_id'] ?? null) !== $product->id || ! is_array($pending['draft'] ?? null)) {
            return back()->withErrors(['otp' => 'Please request a new OTP.']);
        }

        if (($pending['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget('bulk_order_otp');

            return back()->withErrors(['otp' => 'This OTP has expired. Please submit the form again.']);
        }

        $attempts = (int) ($pending['attempts'] ?? 0) + 1;
        if ($attempts > 5) {
            $request->session()->forget('bulk_order_otp');

            return back()->withErrors(['otp' => 'Too many incorrect attempts. Please submit the form again.']);
        }

        $pending['attempts'] = $attempts;
        $request->session()->put('bulk_order_otp', $pending);

        if (! Hash::check($validated['otp'], $pending['otp_hash'] ?? '')) {
            return back()->withErrors(['otp' => 'The OTP is incorrect.'])->withInput();
        }

        $draft = $pending['draft'];
        $request->session()->forget('bulk_order_otp');
        $inquiry = $this->createInquiry($product, $draft);

        $this->sendInquiryNotifications($inquiry);

        return back()
            ->with('bulk_order_success', 'Thank you. Your bulk order request has been received. Our team will contact you shortly.')
            ->with('bulk_order_inquiry_id', $inquiry->id);
    }

    private function sendVerificationOtp(Request $request, Product $product, array $draft): RedirectResponse
    {
        $pending = $request->session()->get('bulk_order_otp');
        if (is_array($pending) && ($pending['product_id'] ?? null) === $product->id) {
            $lastSentAt = (int) ($pending['last_sent_at'] ?? 0);
            if ($lastSentAt > now()->subSeconds(60)->timestamp) {
                return back()->withInput()->withErrors(['email' => 'Please wait a minute before requesting another OTP.']);
            }
        }

        $otp = (string) random_int(100000, 999999);
        $request->session()->put('bulk_order_otp', [
            'product_id' => $product->id,
            'draft' => $draft,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'last_sent_at' => now()->timestamp,
            'attempts' => 0,
        ]);

        try {
            Mail::to($draft['email'], $draft['name'])
                ->sendNow(new \App\Mail\BulkOrderVerificationMail($otp));
        } catch (Throwable $exception) {
            $request->session()->forget('bulk_order_otp');
            Log::error('Bulk order OTP email failed', [
                'product_id' => $product->id,
                'email' => $draft['email'],
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->withErrors(['email' => 'We could not send the verification email. Please try again shortly.']);
        }

        return back()->withInput()->with('bulk_order_otp_sent', 'We sent a 6 digit verification code to your email.');
    }

    private function createInquiry(Product $product, array $draft): BulkOrderInquiry
    {
        return BulkOrderInquiry::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'product_url' => route('product.show', $product->slug),
            'name' => $draft['name'],
            'phone' => $draft['phone'],
            'email' => $draft['email'],
            'quantity' => (int) $draft['quantity'],
            'message' => $draft['message'] ?? null,
            'status' => 'new',
        ]);
    }

    private function sendInquiryNotifications(BulkOrderInquiry $inquiry): void
    {
        try {
            Mail::to($inquiry->email, $inquiry->name)
                ->sendNow(new BulkOrderCustomerConfirmationMail($inquiry));
        } catch (Throwable $exception) {
            Log::error('Bulk order customer email notification failed', [
                'inquiry_id' => $inquiry->id,
                'message' => $exception->getMessage(),
            ]);
        }

        foreach ($this->adminEmails() as $adminEmail) {
            try {
                Mail::to($adminEmail)->sendNow(new BulkOrderAdminNotificationMail($inquiry));
            } catch (Throwable $exception) {
                Log::error('Bulk order admin email notification failed', [
                    'inquiry_id' => $inquiry->id,
                    'recipient' => $adminEmail,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    protected function adminEmails(): array
    {
        return array_values(array_filter(array_map('trim', [
            'sanjayadav448@gmail.com',
            'swatidayal2004@gmail.com',
            'satyamsingh962572@gmail.com',
        ])));
    }
}
