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
            .checkout-options { display: grid; gap: 12px; }
            .payment-option { display: flex; gap: 12px; align-items: flex-start; border: 1px solid #dedede; border-radius: 10px; padding: 14px; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
            .payment-option:hover { border-color: #111; background: #fafafa; }
            .payment-option input { margin-top: 4px; }
            .payment-option-title { font-weight: 700; margin-bottom: 2px; }
            .payment-option-copy { font-size: 13px; color: #666; margin: 0; }
            .summary-card { position: sticky; top: 18px; }
            .summary-items { display: grid; gap: 14px; margin-bottom: 20px; }
            .summary-item { display: grid; grid-template-columns: 56px 1fr auto; gap: 12px; align-items: center; }
            .summary-thumb { width: 56px; height: 70px; border-radius: 8px; object-fit: cover; background: #f3f3f3; }
            .summary-name { margin: 0 0 4px; font-weight: 650; line-height: 1.25; }
            .summary-meta { margin: 0; font-size: 12px; color: #777; }
            .summary-row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; color: #444; }
            .summary-total { border-top: 1px solid #e8e8e8; margin-top: 8px; padding-top: 16px; font-size: 20px; font-weight: 800; color: #111; }
            .checkout-submit { width: 100%; border: 0; margin-top: 20px; }
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
                                    <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name', $user->name ?? '') }}" autocomplete="name">
                                    @error('customer_name') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="customer_email">Email</label>
                                    <input id="customer_email" name="customer_email" type="email" required value="{{ old('customer_email', $user->email ?? '') }}" autocomplete="email">
                                    @error('customer_email') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="customer_phone">Phone</label>
                                    <input id="customer_phone" name="customer_phone" type="tel" required value="{{ old('customer_phone', $user->phone ?? '') }}" autocomplete="tel">
                                    @error('customer_phone') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h4>Shipping Address</h4>
                            <div class="checkout-fields">
                                <div class="checkout-field checkout-field-full">
                                    <label for="shipping_address">Address</label>
                                    <textarea id="shipping_address" name="shipping_address" rows="4" required autocomplete="street-address">{{ old('shipping_address', $user->address ?? '') }}</textarea>
                                    @error('shipping_address') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_city">City</label>
                                    <input id="shipping_city" name="shipping_city" type="text" required value="{{ old('shipping_city') }}" autocomplete="address-level2">
                                    @error('shipping_city') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_state">State</label>
                                    <input id="shipping_state" name="shipping_state" type="text" required value="{{ old('shipping_state') }}" autocomplete="address-level1">
                                    @error('shipping_state') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_pincode">Pincode</label>
                                    <input id="shipping_pincode" name="shipping_pincode" type="text" required value="{{ old('shipping_pincode') }}" autocomplete="postal-code">
                                    @error('shipping_pincode') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="checkout-field">
                                    <label for="shipping_country">Country</label>
                                    <input id="shipping_country" name="shipping_country" type="text" value="{{ old('shipping_country', 'India') }}" autocomplete="country-name">
                                    @error('shipping_country') <div class="field-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="checkout-card">
                            <h4>Payment</h4>
                            <div class="checkout-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                    <span>
                                        <span class="payment-option-title d-block">Cash on Delivery</span>
                                        <span class="payment-option-copy">Pay when your order reaches you.</span>
                                    </span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="razorpay" {{ old('payment_method') === 'razorpay' ? 'checked' : '' }} @disabled(!$razorpayEnabled)>
                                    <span>
                                        <span class="payment-option-title d-block">Pay Online</span>
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
                        <div class="summary-row"><span>Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                        <div class="summary-row"><span>Shipping</span><span>{{ $shippingAmount > 0 ? '₹'.number_format($shippingAmount, 2) : 'Free' }}</span></div>
                        <div class="summary-row"><span>Discount</span><span>₹0.00</span></div>
                        <div class="summary-row summary-total"><span>Total</span><span>₹{{ number_format($subtotal + $shippingAmount, 2) }}</span></div>
                        <button type="submit" id="checkout-submit" class="tf-btn type-xl btn-fill animate-btn checkout-submit">Place Order</button>
                    </aside>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('checkout-form')?.addEventListener('submit', function () {
            const button = document.getElementById('checkout-submit');
            if (button) {
                button.disabled = true;
                button.textContent = 'Processing...';
            }
        });
    </script>
</x-layout>
