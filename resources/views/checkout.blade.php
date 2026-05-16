<x-layout :seo="$seo" title="Checkout">
    <x-slot name="styles">
        <style>
            .checkout-shell { max-width: 1180px; margin: 0 auto; }
            .checkout-grid { display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 28px; align-items: start; }
            .checkout-card { border: 1px solid #e7e7e7; border-radius: 12px; background: #fff; padding: 28px; }
            .checkout-card + .checkout-card { margin-top: 20px; }
            .checkout-card h4 { margin-bottom: 18px; font-size: 20px; }
            .checkout-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
            .checkout-field-full { grid-column: 1 / -1; }
            .checkout-field label { display: block; margin-bottom: 7px; font-size: 13px; font-weight: 600; color: #333; }
            .checkout-field input, .checkout-field textarea { width: 100%; border: 1px solid #d9d9d9; border-radius: 8px; padding: 12px 13px; outline: none; transition: border-color .15s ease, box-shadow .15s ease; }
            .checkout-field input:focus, .checkout-field textarea:focus { border-color: #111; box-shadow: 0 0 0 3px rgba(0,0,0,.06); }
            .checkout-field.is-invalid input, .checkout-field.is-invalid textarea { border-color: #d92d20; background: #fffafa; }
            .checkout-field.is-valid input, .checkout-field.is-valid textarea { border-color: #1a7f37; }
            .field-hint { color: #777; font-size: 12px; line-height: 1.45; margin-top: 5px; }
            .checkout-options { display: grid; gap: 12px; }
            .payment-option { display: flex; gap: 12px; align-items: flex-start; border: 1px solid #dedede; border-radius: 10px; padding: 14px; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
            .payment-option:hover { border-color: #111; background: #fafafa; }
            .payment-option.is-selected { border-color: #111; background: #f8f8f8; }
            .payment-option.is-disabled { opacity: .55; cursor: not-allowed; }
            .payment-option input { margin-top: 4px; }
            .payment-option-title { font-weight: 700; margin-bottom: 2px; }
            .payment-option-copy { font-size: 13px; color: #666; margin: 0; }
            .payment-badge { display: inline-flex; margin-left: 8px; border-radius: 999px; padding: 3px 8px; background: #eef8f1; color: #27633a; font-size: 11px; font-weight: 700; vertical-align: middle; }
            .checkout-nudge { border: 1px solid #d7eadb; background: #f3fbf5; color: #285b36; border-radius: 10px; padding: 12px 14px; font-size: 13px; line-height: 1.45; margin-bottom: 14px; }
            .summary-card { position: sticky; top: 18px; }
            .summary-items { display: grid; gap: 14px; margin-bottom: 20px; }
            .summary-item { display: grid; grid-template-columns: 56px 1fr auto; gap: 12px; align-items: center; }
            .summary-thumb { width: 56px; height: 70px; border-radius: 8px; object-fit: cover; background: #f3f3f3; }
            .summary-name { margin: 0 0 4px; font-weight: 650; line-height: 1.25; }
            .summary-meta { margin: 0; font-size: 12px; color: #777; }
            .summary-row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; color: #444; }
            .summary-row.is-savings { color: #1a7f37; }
            .summary-row.is-muted { color: #777; }
            .summary-total { border-top: 1px solid #e8e8e8; margin-top: 8px; padding-top: 16px; font-size: 20px; font-weight: 800; color: #111; }
            .checkout-submit { width: 100%; border: 0; margin-top: 20px; }
            .checkout-submit:disabled { opacity: .6; cursor: not-allowed; }
            .checkout-alert { border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; }
            .checkout-alert-danger { background: #fff1f1; color: #9f1d1d; border: 1px solid #ffd4d4; }
            .field-error { color: #b42318; font-size: 12px; margin-top: 5px; }
            @media (max-width: 991px) {
                .checkout-grid { grid-template-columns: 1fr; }
                .summary-card { position: static; }
            }
            @media (max-width: 640px) {
                .checkout-card { padding: 20px; border-radius: 10px; }
                .checkout-fields { grid-template-columns: 1fr; }
                .summary-item { grid-template-columns: 48px 1fr; }
                .summary-price { grid-column: 2; }
            }
        </style>
    </x-slot>

    @php
        $oldPayment = old('payment_method', $paymentSettings['cod_enabled'] ? 'cod' : 'razorpay');
        if ($oldPayment === 'cod' && ! $paymentSettings['cod_enabled']) {
            $oldPayment = 'razorpay';
        }
        if ($oldPayment === 'razorpay' && ! $razorpayEnabled) {
            $oldPayment = $paymentSettings['cod_enabled'] ? 'cod' : '';
        }
        $initialTotals = $paymentTotals[$oldPayment] ?? $paymentTotals['cod'];
        $prepaidSavings = (float) ($paymentTotals['razorpay']['payment_discount_amount'] ?? 0);
        $codFee = (float) ($paymentTotals['cod']['payment_fee_amount'] ?? 0);
    @endphp

    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Checkout</p>
                </div>
                <h1>Checkout</h1>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="checkout-shell">
                @if (session('error'))
                    <div class="checkout-alert checkout-alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->has('cart'))
                    <div class="checkout-alert checkout-alert-danger">{{ $errors->first('cart') }}</div>
                @endif

                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form" class="checkout-grid" novalidate>
                    @csrf
                    <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">

                    <div>
                        <div class="checkout-card">
                            <h4>Customer Information</h4>
                            <div class="checkout-fields">
                                <div class="checkout-field checkout-field-full">
                                    <label for="customer_name">Full Name</label>
                                    <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name', $user->name ?? '') }}" autocomplete="name" data-validate="name" data-hint="Please enter your full name.">
                                    <div class="field-hint">Use the name your delivery partner can confirm.</div>
                                    <div class="field-error" data-error-for="customer_name">@error('customer_name') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="customer_email">Email</label>
                                    <input id="customer_email" name="customer_email" type="email" required value="{{ old('customer_email', $user->email ?? '') }}" autocomplete="email" data-validate="email">
                                    <div class="field-error" data-error-for="customer_email">@error('customer_email') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="customer_phone">Phone</label>
                                    <input id="customer_phone" name="customer_phone" type="tel" required value="{{ old('customer_phone', $user->phone ?? '') }}" autocomplete="tel" inputmode="numeric" maxlength="14" data-validate="phone">
                                    <div class="field-hint">Indian mobile number, 10 digits.</div>
                                    <div class="field-error" data-error-for="customer_phone">@error('customer_phone') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h4>Shipping Address</h4>
                            <div class="checkout-fields">
                                <div class="checkout-field checkout-field-full">
                                    <label for="shipping_address">Address</label>
                                    <textarea id="shipping_address" name="shipping_address" rows="4" required autocomplete="street-address" data-validate="address">{{ old('shipping_address', $user->address ?? '') }}</textarea>
                                    <div class="field-hint">House or flat number, street, area, and landmark if useful.</div>
                                    <div class="field-error" data-error-for="shipping_address">@error('shipping_address') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_city">City</label>
                                    <input id="shipping_city" name="shipping_city" type="text" required value="{{ old('shipping_city') }}" autocomplete="address-level2" data-validate="text">
                                    <div class="field-error" data-error-for="shipping_city">@error('shipping_city') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_state">State</label>
                                    <input id="shipping_state" name="shipping_state" type="text" required value="{{ old('shipping_state') }}" autocomplete="address-level1" data-validate="text">
                                    <div class="field-error" data-error-for="shipping_state">@error('shipping_state') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_pincode">Pincode</label>
                                    <input id="shipping_pincode" name="shipping_pincode" type="text" required value="{{ old('shipping_pincode') }}" autocomplete="postal-code" inputmode="numeric" maxlength="6" data-validate="pincode">
                                    <div class="field-error" data-error-for="shipping_pincode">@error('shipping_pincode') {{ $message }} @enderror</div>
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_country">Country</label>
                                    <input id="shipping_country" name="shipping_country" type="text" value="{{ old('shipping_country', 'India') }}" autocomplete="country-name" data-validate="country">
                                    <div class="field-error" data-error-for="shipping_country">@error('shipping_country') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h4>Payment</h4>
                            @if($prepaidSavings > 0 || $paymentSettings['prepaid_free_shipping'])
                                <div class="checkout-nudge">Pay online{{ $prepaidSavings > 0 ? ' and save ₹'.number_format($prepaidSavings, 2) : '' }}{{ $paymentSettings['prepaid_free_shipping'] ? ' with prepaid free shipping' : '' }}.</div>
                            @endif
                            <div class="checkout-options">
                                <label class="payment-option {{ $oldPayment === 'cod' ? 'is-selected' : '' }} {{ ! $paymentSettings['cod_enabled'] ? 'is-disabled' : '' }}">
                                    <input type="radio" name="payment_method" value="cod" {{ $oldPayment === 'cod' ? 'checked' : '' }} @disabled(! $paymentSettings['cod_enabled'])>
                                    <span>
                                        <span class="payment-option-title d-block">Cash on Delivery @if($codFee > 0)<span class="payment-badge">+₹{{ number_format($codFee, 2) }}</span>@endif</span>
                                        <span class="payment-option-copy">{{ $paymentSettings['cod_enabled'] ? 'Pay when your order reaches you.' : 'Cash on Delivery is temporarily unavailable.' }}</span>
                                    </span>
                                </label>
                                <label class="payment-option {{ $oldPayment === 'razorpay' ? 'is-selected' : '' }} {{ ! $razorpayEnabled ? 'is-disabled' : '' }}">
                                    <input type="radio" name="payment_method" value="razorpay" {{ $oldPayment === 'razorpay' ? 'checked' : '' }} @disabled(!$razorpayEnabled)>
                                    <span>
                                        <span class="payment-option-title d-block">Pay Online @if($prepaidSavings > 0)<span class="payment-badge">Save ₹{{ number_format($prepaidSavings, 2) }}</span>@endif</span>
                                        <span class="payment-option-copy">{{ $razorpayEnabled ? 'Pay securely with UPI, cards, net banking, or wallets.' : 'Online payment is temporarily unavailable.' }}</span>
                                    </span>
                                </label>
                            </div>
                            @error('payment_method') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <aside class="checkout-card summary-card">
                        <h4>Order Summary</h4>
                        <div class="summary-items">
                            @foreach ($items as $item)
                                @php
                                    $image = $item->product?->images?->first()?->image_path;
                                @endphp
                                <div class="summary-item">
                                    @if($image)
                                        <img class="summary-thumb" src="{{ asset('storage/' . $image) }}" alt="{{ $item->product->name ?? 'Product' }}">
                                    @else
                                        <div class="summary-thumb"></div>
                                    @endif
                                    <div>
                                        <p class="summary-name">{{ $item->product->name ?? 'Product' }}</p>
                                        <p class="summary-meta">
                                            Qty {{ $item->quantity }}
                                            @if($item->variant)
                                                · {{ collect([$item->variant->color, $item->variant->size])->filter()->implode(' / ') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="summary-price fw-semibold">₹{{ number_format($item->price * $item->quantity, 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="summary-row"><span>Subtotal</span><span data-summary="subtotal">₹{{ number_format($initialTotals['subtotal'], 2) }}</span></div>
                        <div class="summary-row"><span>Shipping</span><span data-summary="shipping">{{ $initialTotals['shipping_amount'] > 0 ? '₹'.number_format($initialTotals['shipping_amount'], 2) : 'Free' }}</span></div>
                        <div class="summary-row {{ $initialTotals['payment_fee_amount'] > 0 ? '' : 'is-muted' }}" data-row="payment_fee"><span>Cash Handling Fee</span><span data-summary="payment_fee">{{ $initialTotals['payment_fee_amount'] > 0 ? '₹'.number_format($initialTotals['payment_fee_amount'], 2) : '₹0.00' }}</span></div>
                        <div class="summary-row is-savings" data-row="payment_discount"><span>Prepaid Savings</span><span data-summary="payment_discount">{{ $initialTotals['payment_discount_amount'] > 0 ? '-₹'.number_format($initialTotals['payment_discount_amount'], 2) : '₹0.00' }}</span></div>
                        <div class="summary-row"><span>Discount</span><span>₹0.00</span></div>
                        <div class="summary-row summary-total"><span>Total</span><span data-summary="total">₹{{ number_format($initialTotals['total_amount'], 2) }}</span></div>
                        <button type="submit" id="checkout-submit" class="tf-btn type-xl btn-fill animate-btn checkout-submit" disabled>Place Order</button>
                    </aside>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const form = document.getElementById('checkout-form');
            if (!form) return;

            const totals = @json($paymentTotals);
            const submit = document.getElementById('checkout-submit');
            const touched = new Set();
            const formatMoney = amount => Number(amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const fakePhones = new Set(['9999999999', '1234567890', '0000000000']);
            const disposableDomains = new Set(['mailinator.com', 'tempmail.com', '10minutemail.com', 'guerrillamail.com', 'yopmail.com', 'trashmail.com']);

            const validators = {
                name(value) {
                    const clean = value.trim().replace(/\s+/g, ' ');
                    if (!clean) return 'Please enter your full name';
                    if (clean.length < 3) return 'Name looks too short';
                    if (!/[A-Za-z]/.test(clean) || /^[^A-Za-z]+$/.test(clean)) return 'Please enter your full name';
                    if (clean.split(' ').filter(Boolean).length < 2 && clean.length < 6) return 'Name looks too short';
                    return '';
                },
                email(value) {
                    const clean = value.trim().toLowerCase();
                    if (!clean) return 'Email address is required';
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(clean)) return 'Please enter a valid email address';
                    if (disposableDomains.has(clean.split('@').pop())) return 'Please use an email address you can access for order updates';
                    return '';
                },
                phone(value) {
                    const digits = value.replace(/\D+/g, '');
                    if (!digits) return 'Enter a valid 10-digit mobile number';
                    if (!/^[6-9]\d{9}$/.test(digits)) return 'Enter a valid 10-digit mobile number';
                    if (fakePhones.has(digits) || /^(\d)\1{9}$/.test(digits)) return 'This phone number looks invalid';
                    return '';
                },
                address(value) {
                    const clean = value.trim().replace(/\s+/g, ' ');
                    const words = clean.split(' ').filter(Boolean);
                    const compact = clean.replace(/[^A-Za-z0-9]/g, '');
                    if (!clean || clean.length < 15 || words.length < 3 || compact.length < 10) return 'Please enter a complete delivery address';
                    if (!/[A-Za-z]/.test(clean) || /^[^A-Za-z0-9]+$/.test(clean)) return 'Please enter a complete delivery address';
                    if (/(.{2,})\1{4,}/i.test(clean)) return 'Please enter a complete delivery address';
                    return '';
                },
                text(value) {
                    const clean = value.trim();
                    if (!clean) return 'This field is required';
                    if (!/^[A-Za-z .-]{2,}$/.test(clean)) return 'Use letters only here';
                    return '';
                },
                country(value) {
                    const clean = value.trim();
                    if (clean && !/^[A-Za-z .-]{2,}$/.test(clean)) return 'Use letters only here';
                    return '';
                },
                pincode(value) {
                    const digits = value.replace(/\D+/g, '');
                    if (!/^[1-9]\d{5}$/.test(digits)) return 'Enter a valid 6-digit pincode';
                    return '';
                }
            };

            function setFieldState(input, force) {
                const type = input.dataset.validate;
                if (!type) return true;
                const field = input.closest('.checkout-field');
                const error = form.querySelector(`[data-error-for="${input.name}"]`);
                const message = validators[type](input.value);
                const show = force || touched.has(input.name);

                field?.classList.toggle('is-invalid', show && Boolean(message));
                field?.classList.toggle('is-valid', show && !message && input.value.trim() !== '');
                if (error && show) error.textContent = message;
                return !message;
            }

            function validateForm(force = false) {
                let valid = true;
                form.querySelectorAll('[data-validate]').forEach(input => {
                    valid = setFieldState(input, force) && valid;
                });
                const payment = form.querySelector('input[name="payment_method"]:checked');
                valid = Boolean(payment) && valid;
                if (submit && submit.textContent !== 'Processing...') submit.disabled = !valid;
                return valid;
            }

            function updateTotals() {
                const method = form.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
                const selected = totals[method] || totals.cod;
                const feeRow = form.querySelector('[data-row="payment_fee"]');
                const discountRow = form.querySelector('[data-row="payment_discount"]');

                form.querySelector('[data-summary="subtotal"]').textContent = `₹${formatMoney(selected.subtotal)}`;
                form.querySelector('[data-summary="shipping"]').textContent = Number(selected.shipping_amount) > 0 ? `₹${formatMoney(selected.shipping_amount)}` : 'Free';
                form.querySelector('[data-summary="payment_fee"]').textContent = Number(selected.payment_fee_amount) > 0 ? `₹${formatMoney(selected.payment_fee_amount)}` : '₹0.00';
                form.querySelector('[data-summary="payment_discount"]').textContent = Number(selected.payment_discount_amount) > 0 ? `-₹${formatMoney(selected.payment_discount_amount)}` : '₹0.00';
                form.querySelector('[data-summary="total"]').textContent = `₹${formatMoney(selected.total_amount)}`;
                feeRow?.classList.toggle('is-muted', Number(selected.payment_fee_amount) <= 0);
                discountRow?.classList.toggle('is-muted', Number(selected.payment_discount_amount) <= 0);

                form.querySelectorAll('.payment-option').forEach(label => {
                    label.classList.toggle('is-selected', label.querySelector('input')?.checked);
                });
            }

            form.querySelectorAll('[data-validate]').forEach(input => {
                input.addEventListener('blur', () => {
                    input.value = input.value.trim().replace(/\s+/g, ' ');
                    if (input.dataset.validate === 'email') input.value = input.value.toLowerCase();
                    if (['phone', 'pincode'].includes(input.dataset.validate)) input.value = input.value.replace(/\D+/g, '');
                    touched.add(input.name);
                    validateForm();
                });
                input.addEventListener('input', () => {
                    if (touched.has(input.name)) setFieldState(input, false);
                    validateForm();
                });
            });

            form.querySelectorAll('input[name="payment_method"]').forEach(input => {
                input.addEventListener('change', () => {
                    updateTotals();
                    validateForm();
                });
            });

            form.addEventListener('submit', function (event) {
                form.querySelectorAll('[data-validate]').forEach(input => touched.add(input.name));
                if (!validateForm(true)) {
                    event.preventDefault();
                    const firstError = form.querySelector('.checkout-field.is-invalid input, .checkout-field.is-invalid textarea');
                    firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => firstError?.focus(), 250);
                    return;
                }

                if (submit) {
                    submit.disabled = true;
                    submit.textContent = 'Processing...';
                }
            });

            updateTotals();
            validateForm();
        })();
    </script>
</x-layout>
