<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OtpAuthController extends Controller
{
    public function sendOtp(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('auth_modal', 'sign');
        }

        $validated = $validator->validated();

        $this->storeAndSendOtp($request, $validated['email']);

        return back()->with([
            'auth_modal' => 'sign',
            'otp_email' => $validated['email'],
            'success' => 'We sent a 6 digit OTP to your email.',
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'auth_modal' => 'sign',
                    'otp_email' => $request->input('email'),
                ]);
        }

        $validated = $validator->validated();
        $otpError = $this->pendingOtpError($request, $validated['email'], $validated['otp']);

        if ($otpError) {
            return back()
                ->withErrors(['otp' => $otpError])
                ->withInput()
                ->with([
                    'auth_modal' => 'sign',
                    'otp_email' => $validated['email'],
                ]);
        }

        $user = User::firstOrCreate([
            'email' => $validated['email'],
        ], [
            'name' => $this->nameFromEmail($validated['email']),
            'email' => $validated['email'],
            'email_verified_at' => now(),
            'password' => Str::random(40),
            'role' => 'customer',
            'status' => true,
        ]);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('otp_auth');

        return redirect()->intended(route('home'))->with('success', 'You are signed in.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function storeAndSendOtp(Request $request, string $email): void
    {
        $otp = (string) random_int(100000, 999999);

        $request->session()->put('otp_auth', [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        Mail::send('emails.auth-otp', [
            'otp' => $otp,
        ], function ($message) use ($email) {
            $message->to($email)->subject('Your KraftX OTP');
        });
    }

    private function pendingOtpError(Request $request, string $email, string $otp): ?string
    {
        $pending = $request->session()->get('otp_auth');

        if (
            ! $pending ||
            ($pending['email'] ?? null) !== $email
        ) {
            return 'Please request a new OTP.';
        }

        if (($pending['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget('otp_auth');

            return 'This OTP has expired. Please request a new one.';
        }

        if (! Hash::check($otp, $pending['otp_hash'] ?? '')) {
            return 'The OTP is incorrect.';
        }

        return null;
    }

    private function nameFromEmail(string $email): string
    {
        return Str::of($email)
            ->before('@')
            ->replace(['.', '_', '-'], ' ')
            ->title()
            ->toString();
    }
}
