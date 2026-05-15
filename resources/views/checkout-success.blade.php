<x-layout :seo="$seo" title="Order Confirmed">
    @php
        $supportEmail = config('seo.support_email', 'thekraftxofficial@gmail.com');
        $isCod = $order->payment_method === 'COD';
        $paymentLabel = $isCod ? 'Cash on Delivery' : 'Online payment';
        $deliveryMessage = $order->estimated_delivery_date
            ? 'Estimated delivery by ' . $order->estimated_delivery_date->format('d M Y')
            : 'Estimated delivery details will be shared after dispatch.';
    @endphp

    <x-slot:styles>
        <style>
            .success-page {
                background: #f7f4ef;
                padding: 46px 0 64px;
            }
            .success-hero {
                position: relative;
                overflow: hidden;
                background: #111;
                color: #fff;
                border-radius: 16px;
                padding: 42px;
                margin-bottom: 28px;
                box-shadow: 0 22px 54px rgba(20, 16, 12, .16);
            }
            .success-hero h1,
            .success-hero p {
                color: #fff;
            }
            .success-mark {
                width: 72px;
                height: 72px;
                border-radius: 50%;
                background: #f3efe7;
                color: #111;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 22px;
                position: relative;
            }
            .success-mark svg {
                width: 38px;
                height: 38px;
            }
            .success-mark path {
                stroke-dasharray: 44;
                stroke-dashoffset: 44;
                animation: draw-check .7s ease forwards .2s;
            }
            .success-copy {
                max-width: 690px;
                font-size: 17px;
                line-height: 1.7;
                opacity: .88;
                margin-bottom: 0;
            }
            .confetti {
                position: absolute;
                inset: 0;
                pointer-events: none;
                opacity: .45;
            }
            .confetti span {
                position: absolute;
                width: 7px;
                height: 12px;
                background: #e6d2a3;
                border-radius: 2px;
                animation: soft-fall 2.8s ease-in-out infinite;
            }
            .confetti span:nth-child(1) { top: 14%; left: 72%; animation-delay: .1s; }
            .confetti span:nth-child(2) { top: 24%; left: 84%; background: #c7dfc5; animation-delay: .45s; }
            .confetti span:nth-child(3) { top: 58%; left: 78%; background: #f0b8a8; animation-delay: .2s; }
            .confetti span:nth-child(4) { top: 30%; left: 92%; animation-delay: .7s; }
            .confetti span:nth-child(5) { top: 68%; left: 90%; background: #d5c0f0; animation-delay: .35s; }
            .success-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
                gap: 28px;
                align-items: start;
            }
            .success-card {
                background: #fff;
                border: 1px solid rgba(33, 28, 24, .10);
                border-radius: 14px;
                box-shadow: 0 18px 44px rgba(28, 24, 19, .07);
                overflow: hidden;
            }
            .success-card-inner {
                padding: 28px;
            }
            .section-heading {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 20px;
            }
            .section-heading h4,
            .section-heading h5 {
                margin: 0;
            }
            .status-pill {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                border-radius: 999px;
                padding: 7px 11px;
                background: #eef8f1;
                color: #27633a;
                font-size: 13px;
                font-weight: 700;
                white-space: nowrap;
            }
            .order-item {
                display: grid;
                grid-template-columns: 74px minmax(0, 1fr) auto;
                gap: 14px;
                align-items: center;
                padding: 16px 0;
                border-top: 1px solid rgba(33, 28, 24, .08);
            }
            .order-item:first-of-type {
                border-top: 0;
                padding-top: 0;
            }
            .order-thumb {
                width: 74px;
                height: 90px;
                border-radius: 10px;
                background: #f3efe7;
                object-fit: cover;
            }
            .order-item-name {
                font-weight: 700;
                margin-bottom: 5px;
            }
            .order-item-meta {
                color: #756b62;
                font-size: 13px;
                margin: 0;
            }
            .summary-lines {
                display: grid;
                gap: 11px;
            }
            .summary-line {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                color: #6d6259;
            }
            .summary-line strong {
                color: #111;
            }
            .summary-total {
                border-top: 1px solid rgba(33, 28, 24, .12);
                padding-top: 16px;
                margin-top: 6px;
                font-size: 19px;
                font-weight: 800;
                color: #111;
            }
            .address-box {
                margin-top: 20px;
                padding: 18px;
                border-radius: 12px;
                background: #fbfaf8;
                border: 1px solid rgba(33, 28, 24, .08);
                color: #6d6259;
                line-height: 1.65;
            }
            .address-box strong {
                color: #111;
            }
            .steps-list {
                display: grid;
                gap: 16px;
            }
            .step-item {
                display: grid;
                grid-template-columns: 34px minmax(0, 1fr);
                gap: 12px;
            }
            .step-number {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: #111;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 13px;
            }
            .step-item strong {
                display: block;
                margin-bottom: 3px;
            }
            .step-item p {
                color: #6d6259;
                margin: 0;
                line-height: 1.55;
            }
            .cta-stack {
                display: grid;
                gap: 12px;
            }
            .support-note {
                margin: 18px 0 0;
                color: #6d6259;
                font-size: 14px;
                line-height: 1.6;
            }
            @keyframes draw-check {
                to { stroke-dashoffset: 0; }
            }
            @keyframes soft-fall {
                0%, 100% { transform: translateY(0) rotate(0); opacity: .35; }
                50% { transform: translateY(16px) rotate(16deg); opacity: .8; }
            }
            @media (max-width: 991px) {
                .success-grid {
                    grid-template-columns: 1fr;
                }
            }
            @media (max-width: 575px) {
                .success-page {
                    padding: 28px 0 42px;
                }
                .success-hero,
                .success-card-inner {
                    padding: 24px 18px;
                }
                .success-mark {
                    width: 62px;
                    height: 62px;
                }
                .order-item {
                    grid-template-columns: 62px minmax(0, 1fr);
                }
                .order-item-price {
                    grid-column: 2;
                    justify-self: start;
                }
                .order-thumb {
                    width: 62px;
                    height: 76px;
                }
                .section-heading {
                    align-items: flex-start;
                    flex-direction: column;
                }
            }
        </style>
    </x-slot:styles>

    <section class="success-page">
        <div class="container">
            <div class="success-hero">
                <div class="confetti" aria-hidden="true">
                    <span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="success-mark" aria-hidden="true">
                    <svg viewBox="0 0 52 52" fill="none">
                        <path d="M14 27.5L22.5 36L39 17" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="text-caption-01 mb-8">Order {{ $order->order_number }}</p>
                <h1 class="mb-14">Your order is confirmed.</h1>
                <p class="success-copy">
                    Thank you, {{ $order->customer_name }}. We have received your order and sent the confirmation details to {{ $order->customer_email }}.
                    Our team will prepare your KraftX pieces with care and keep you updated as the order moves forward.
                </p>
            </div>

            <div class="success-grid">
                <div class="success-card">
                    <div class="success-card-inner">
                        <div class="section-heading">
                            <h4>Order Summary</h4>
                            <span class="status-pill"><i class="icon icon-check"></i> Confirmed</span>
                        </div>

                        @foreach($order->items as $item)
                            @php
                                $imagePath = $item->product?->images?->first()?->image_path;
                                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : asset('assets/images/product/product-placeholder.jpg');
                            @endphp
                            <div class="order-item">
                                <img class="order-thumb" src="{{ $imageUrl }}" alt="{{ $item->name }}">
                                <div>
                                    <div class="order-item-name">{{ $item->name }}</div>
                                    <p class="order-item-meta">{{ $item->sku }} · Qty {{ $item->quantity }}</p>
                                </div>
                                <strong class="order-item-price">₹{{ number_format($item->total, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="success-card">
                    <div class="success-card-inner">
                        <div class="section-heading">
                            <h5>Payment & Delivery</h5>
                        </div>
                        <div class="summary-lines">
                            <div class="summary-line">
                                <span>Payment method</span>
                                <strong>{{ $paymentLabel }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Payment status</span>
                                <strong>{{ \Illuminate\Support\Str::title($order->payment_status) }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Subtotal</span>
                                <strong>₹{{ number_format($order->subtotal, 2) }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Shipping</span>
                                <strong>{{ (float) $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount, 2) : 'Free' }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Discount</span>
                                <strong>{{ (float) $order->discount_amount > 0 ? '-₹' . number_format($order->discount_amount, 2) : '₹0.00' }}</strong>
                            </div>
                            <div class="summary-line summary-total">
                                <span>Total</span>
                                <span>₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="address-box">
                            <strong>Delivery address</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}<br>
                            {{ $order->shipping_country }}<br>
                            <span>{{ $deliveryMessage }}</span>
                        </div>
                    </div>
                </aside>

                <div class="success-card">
                    <div class="success-card-inner">
                        <div class="section-heading">
                            <h5>What Happens Next</h5>
                        </div>
                        <div class="steps-list">
                            <div class="step-item">
                                <span class="step-number">1</span>
                                <div>
                                    <strong>Confirmation email sent</strong>
                                    <p>Your order details, payment summary, and delivery address have been emailed to you.</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <span class="step-number">2</span>
                                <div>
                                    <strong>Order review and packing</strong>
                                    <p>We will review the order and prepare it for dispatch. COD orders may receive a confirmation call if needed.</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <span class="step-number">3</span>
                                <div>
                                    <strong>Delivery updates</strong>
                                    <p>{{ $deliveryMessage }} Tracking details will appear once the shipment is ready.</p>
                                </div>
                            </div>
                        </div>
                        <p class="support-note">Need help with this order? Email <a href="mailto:{{ $supportEmail }}" class="link">{{ $supportEmail }}</a> and mention {{ $order->order_number }}.</p>
                    </div>
                </div>

                <aside class="success-card">
                    <div class="success-card-inner">
                        <div class="section-heading">
                            <h5>Continue</h5>
                        </div>
                        <div class="cta-stack">
                            <a href="{{ route('home') }}" class="tf-btn btn-fill animate-btn w-100">Continue Shopping</a>
                            <a href="{{ route('track.order') }}" class="tf-btn btn-stroke animate-btn w-100">Track Order</a>
                            <a href="{{ route('account.orders') }}" class="tf-btn btn-stroke animate-btn w-100">View My Orders</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-layout>
