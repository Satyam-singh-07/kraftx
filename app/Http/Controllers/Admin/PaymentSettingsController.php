<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PaymentStrategy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentSettingsController extends Controller
{
    public function __construct(
        protected PaymentStrategy $paymentStrategy
    ) {
    }

    public function edit(): View
    {
        return view('admin.settings.payment', [
            'settings' => $this->paymentStrategy->settings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cod_enabled' => ['nullable', 'boolean'],
            'cod_fee_enabled' => ['nullable', 'boolean'],
            'cod_fee_amount' => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'cod_free_above' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'prepaid_discount_enabled' => ['nullable', 'boolean'],
            'prepaid_discount_amount' => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'prepaid_free_shipping' => ['nullable', 'boolean'],
        ]);

        $this->paymentStrategy->save([
            'cod_enabled' => $request->boolean('cod_enabled'),
            'cod_fee_enabled' => $request->boolean('cod_fee_enabled'),
            'cod_fee_amount' => round((float) ($validated['cod_fee_amount'] ?? 0), 2),
            'cod_free_above' => round((float) ($validated['cod_free_above'] ?? 0), 2),
            'prepaid_discount_enabled' => $request->boolean('prepaid_discount_enabled'),
            'prepaid_discount_amount' => round((float) ($validated['prepaid_discount_amount'] ?? 0), 2),
            'prepaid_free_shipping' => $request->boolean('prepaid_free_shipping'),
        ]);

        return back()->with('success', 'Payment strategy settings updated.');
    }
}
