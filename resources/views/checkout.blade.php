<x-layout :seo="$seo" title="Checkout">
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
            @if (session('error'))
                <div class="alert alert-danger mb-24">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-24">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" class="row g-4">
                @csrf
                <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
                <div class="col-lg-7">
                    <div class="border rounded-20 p-30">
                        <h4 class="mb-24">Delivery Details</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <fieldset class="tf-field">
                                    <label for="customer_name" class="tf-lable fw-medium">Full Name <span class="text-primary">*</span></label>
                                    <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name', $user->name ?? '') }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="customer_email" class="tf-lable fw-medium">Email <span class="text-primary">*</span></label>
                                    <input id="customer_email" name="customer_email" type="email" required value="{{ old('customer_email', $user->email ?? '') }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="customer_phone" class="tf-lable fw-medium">Phone <span class="text-primary">*</span></label>
                                    <input id="customer_phone" name="customer_phone" type="text" required value="{{ old('customer_phone', $user->phone ?? '') }}">
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <fieldset class="tf-field d-flex flex-column">
                                    <label for="shipping_address" class="tf-lable fw-medium">Address <span class="text-primary">*</span></label>
                                    <textarea id="shipping_address" name="shipping_address" rows="4" required>{{ old('shipping_address', $user->address ?? '') }}</textarea>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="shipping_city" class="tf-lable fw-medium">City <span class="text-primary">*</span></label>
                                    <input id="shipping_city" name="shipping_city" type="text" required value="{{ old('shipping_city') }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="shipping_state" class="tf-lable fw-medium">State <span class="text-primary">*</span></label>
                                    <input id="shipping_state" name="shipping_state" type="text" required value="{{ old('shipping_state') }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="shipping_pincode" class="tf-lable fw-medium">Pincode <span class="text-primary">*</span></label>
                                    <input id="shipping_pincode" name="shipping_pincode" type="text" required value="{{ old('shipping_pincode') }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="tf-field">
                                    <label for="shipping_country" class="tf-lable fw-medium">Country</label>
                                    <input id="shipping_country" name="shipping_country" type="text" value="{{ old('shipping_country', 'India') }}">
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <fieldset class="tf-field d-flex flex-column">
                                    <label for="notes" class="tf-lable fw-medium">Order Notes</label>
                                    <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="border rounded-20 p-30 position-sticky" style="top: 20px;">
                        <h4 class="mb-24">Order Summary</h4>
                        <div class="d-grid gap-16 mb-24">
                            @foreach ($items as $item)
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <p class="fw-medium mb-4">{{ $item->product->name ?? 'Product' }}</p>
                                        <p class="text-caption-01 cl-text-2 mb-0">
                                            Qty {{ $item->quantity }}
                                            @if($item->variant)
                                                · {{ collect([$item->variant->color, $item->variant->size])->filter()->implode(' / ') }}
                                            @endif
                                        </p>
                                    </div>
                                    <p class="fw-medium mb-0">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-top pt-16 d-grid gap-10">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Shipping</span>
                                <span>{{ $shippingAmount > 0 ? '₹'.number_format($shippingAmount, 2) : 'Free' }}</span>
                            </div>
                            <div class="d-flex justify-content-between h5 mb-0 border-top pt-16">
                                <span>Total</span>
                                <span>₹{{ number_format($subtotal + $shippingAmount, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-24 p-16 rounded-12 bg-light">
                            <p class="fw-medium mb-4">Payment Method</p>
                            <p class="text-caption-01 cl-text-2 mb-0">Cash on Delivery. Payment will remain pending until collected.</p>
                        </div>
                        <button type="submit" class="tf-btn type-xl btn-fill animate-btn w-100 mt-24">Place Order</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-layout>
