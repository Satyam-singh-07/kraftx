<x-layout :seo="$seo" title="Complete Payment">
    @php
        $itemCount = $order->items->sum('quantity');
        $supportEmail = config('seo.support_email', 'thekraftxofficial@gmail.com');
    @endphp

    <x-slot:styles>
        <style>
            .payment-handoff {
                background: #f7f4ef;
                padding: 56px 0;
            }
            .payment-shell {
                display: grid;
                grid-template-columns: minmax(0, 1.08fr) minmax(320px, .92fr);
                gap: 28px;
                align-items: start;
            }
            .payment-panel {
                background: #fff;
                border: 1px solid rgba(33, 28, 24, .10);
                border-radius: 14px;
                box-shadow: 0 18px 44px rgba(28, 24, 19, .07);
                overflow: hidden;
            }
            .payment-main {
                padding: 34px;
            }
            .payment-kicker {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                background: #eef8f1;
                color: #27633a;
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 18px;
            }
            .payment-title {
                font-size: clamp(28px, 4vw, 44px);
                line-height: 1.08;
                margin-bottom: 12px;
                letter-spacing: 0;
            }
            .payment-copy {
                color: #6d6259;
                font-size: 16px;
                line-height: 1.7;
                max-width: 620px;
                margin-bottom: 28px;
            }
            .status-box {
                border: 1px solid rgba(33, 28, 24, .10);
                border-radius: 12px;
                padding: 22px;
                background: #fbfaf8;
                margin-bottom: 22px;
            }
            .status-row {
                display: flex;
                gap: 16px;
                align-items: center;
            }
            .secure-loader {
                width: 46px;
                height: 46px;
                border-radius: 50%;
                border: 3px solid #ded5cb;
                border-top-color: #111;
                animation: handoff-spin 1s linear infinite;
                flex: 0 0 auto;
            }
            .status-title {
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 4px;
            }
            .status-text {
                color: #6d6259;
                margin: 0;
            }
            .progress-track {
                height: 5px;
                background: #ebe5dd;
                border-radius: 999px;
                overflow: hidden;
                margin-top: 18px;
            }
            .progress-bar-soft {
                width: 42%;
                height: 100%;
                background: #111;
                border-radius: inherit;
                animation: handoff-progress 1.8s ease-in-out infinite;
            }
            .trust-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                margin: 22px 0 28px;
            }
            .trust-item {
                border: 1px solid rgba(33, 28, 24, .10);
                border-radius: 10px;
                padding: 14px;
                background: #fff;
            }
            .trust-item i {
                font-size: 20px;
                display: inline-block;
                margin-bottom: 8px;
            }
            .trust-item strong {
                display: block;
                font-size: 14px;
                margin-bottom: 3px;
            }
            .trust-item span {
                color: #756b62;
                font-size: 13px;
                line-height: 1.45;
            }
            .payment-actions {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .payment-help {
                color: #6d6259;
                font-size: 13px;
                text-align: center;
                margin: 0;
            }
            .payment-help a {
                color: #111;
                text-decoration: underline;
            }
            .payment-alert {
                display: none;
                border-radius: 10px;
                background: #fff6e7;
                border: 1px solid #f0c98a;
                color: #6d4a12;
                padding: 13px 14px;
                font-size: 14px;
            }
            .summary-head {
                padding: 24px 26px;
                border-bottom: 1px solid rgba(33, 28, 24, .10);
                background: #111;
                color: #fff;
            }
            .summary-head p,
            .summary-head h5 {
                color: #fff;
            }
            .summary-body {
                padding: 24px 26px;
            }
            .summary-line {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                padding: 10px 0;
                color: #6d6259;
            }
            .summary-line strong {
                color: #111;
            }
            .summary-total {
                border-top: 1px solid rgba(33, 28, 24, .12);
                margin-top: 10px;
                padding-top: 18px;
                font-size: 20px;
                font-weight: 800;
                color: #111;
            }
            .summary-meta {
                display: grid;
                gap: 10px;
                margin-top: 18px;
                padding-top: 18px;
                border-top: 1px solid rgba(33, 28, 24, .10);
                color: #6d6259;
                font-size: 14px;
            }
            .summary-meta strong {
                color: #111;
            }
            #pay-now[disabled],
            #retry-payment[disabled] {
                opacity: .72;
                cursor: not-allowed;
            }
            #retry-payment {
                display: none;
            }
            @keyframes handoff-spin {
                to { transform: rotate(360deg); }
            }
            @keyframes handoff-progress {
                0% { transform: translateX(-110%); width: 38%; }
                50% { width: 58%; }
                100% { transform: translateX(270%); width: 38%; }
            }
            @media (max-width: 991px) {
                .payment-shell {
                    grid-template-columns: 1fr;
                }
                .payment-handoff {
                    padding: 34px 0;
                }
            }
            @media (max-width: 575px) {
                .payment-main,
                .summary-body,
                .summary-head {
                    padding: 22px 18px;
                }
                .trust-grid {
                    grid-template-columns: 1fr;
                }
                .status-row {
                    align-items: flex-start;
                }
                .secure-loader {
                    width: 38px;
                    height: 38px;
                }
            }
        </style>
    </x-slot:styles>

    <section class="payment-handoff">
        <div class="container">
            <div class="payment-shell">
                <div class="payment-panel payment-main">
                    @if(session('error'))
                        <div class="alert alert-danger mb-20">{{ session('error') }}</div>
                    @endif

                    <div class="payment-kicker">
                        <i class="icon icon-ShieldCheck"></i>
                        Secure Razorpay checkout
                    </div>
                    <h1 class="payment-title">One final step to confirm your order.</h1>
                    <p class="payment-copy">
                        We are preparing an encrypted payment window for order {{ $order->order_number }}.
                        Your card, UPI, wallet, and netbanking details are handled securely by Razorpay.
                    </p>

                    <div class="status-box">
                        <div class="status-row">
                            <div class="secure-loader" aria-hidden="true"></div>
                            <div>
                                <div class="status-title" id="payment-status-title">Preparing secure payment...</div>
                                <p class="status-text" id="payment-status-text">The payment popup should open after you tap the button below. If your browser blocks it, retry from this page.</p>
                            </div>
                        </div>
                        <div class="progress-track" aria-hidden="true">
                            <div class="progress-bar-soft"></div>
                        </div>
                    </div>

                    <div class="payment-alert" id="payment-alert">
                        The payment popup did not open. Please allow popups for this site and try again.
                    </div>

                    <div class="trust-grid">
                        <div class="trust-item">
                            <i class="icon icon-ShieldCheck"></i>
                            <strong>Encrypted payment</strong>
                            <span>KraftX never stores your card, UPI, or banking credentials.</span>
                        </div>
                        <div class="trust-item">
                            <i class="icon icon-ArrowClockwise"></i>
                            <strong>Refund support</strong>
                            <span>Eligible refund and replacement requests are handled by our support team.</span>
                        </div>
                        <div class="trust-item">
                            <i class="icon icon-Package"></i>
                            <strong>Careful dispatch</strong>
                            <span>Your order is reviewed, packed, and prepared for safe delivery.</span>
                        </div>
                        <div class="trust-item">
                            <i class="icon icon-Headset"></i>
                            <strong>Need help?</strong>
                            <span>Reach us at {{ $supportEmail }} for payment or order support.</span>
                        </div>
                    </div>

                    <div class="payment-actions">
                        <button id="pay-now" class="tf-btn type-xl btn-fill animate-btn w-100">Proceed to Payment</button>
                        <button id="retry-payment" class="tf-btn type-xl btn-stroke animate-btn w-100" type="button">Retry Payment</button>
                        <p class="payment-help">Do not refresh after payment. We will verify it securely and show your confirmation page.</p>
                    </div>
                </div>

                <aside class="payment-panel">
                    <div class="summary-head">
                        <p class="text-caption-01 mb-6">Payment summary</p>
                        <h5 class="mb-0">{{ $order->order_number }}</h5>
                    </div>
                    <div class="summary-body">
                        <div class="summary-line">
                            <span>Customer</span>
                            <strong>{{ $order->customer_name }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Items</span>
                            <strong>{{ $itemCount }} {{ \Illuminate\Support\Str::plural('item', $itemCount) }}</strong>
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
                            <span>Payable now</span>
                            <span>₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="summary-meta">
                            <div><strong>Payment method:</strong> Online payment</div>
                            <div><strong>Delivery:</strong> We will send confirmation and processing updates by email.</div>
                            <div><strong>Support:</strong> {{ $supportEmail }}</div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <form id="razorpay-verify-form" action="{{ route('payments.razorpay.verify', $order) }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const payButton = document.getElementById('pay-now');
        const retryButton = document.getElementById('retry-payment');
        const alertBox = document.getElementById('payment-alert');
        const statusTitle = document.getElementById('payment-status-title');
        const statusText = document.getElementById('payment-status-text');

        const setLoading = (loading) => {
            if (payButton) {
                payButton.disabled = loading;
                payButton.textContent = loading ? 'Opening secure payment...' : 'Proceed to Payment';
            }
            if (retryButton) {
                retryButton.disabled = loading;
            }
        };

        const showRetry = (message) => {
            setLoading(false);
            if (alertBox) {
                alertBox.textContent = message;
                alertBox.style.display = 'block';
            }
            if (retryButton) {
                retryButton.style.display = 'block';
            }
            if (statusTitle) {
                statusTitle.textContent = 'Payment popup needs your attention';
            }
            if (statusText) {
                statusText.textContent = 'Please retry once popups are allowed, or contact support if the issue continues.';
            }
        };

        const options = {
            key: @json($razorpayKey),
            amount: {{ (int) round($order->total_amount * 100) }},
            currency: 'INR',
            name: @json(config('app.name', 'KraftX')),
            description: @json($order->order_number),
            order_id: @json($order->payment_reference),
            prefill: {
                name: @json($order->customer_name),
                email: @json($order->customer_email),
                contact: @json($order->customer_phone)
            },
            handler(response) {
                if (statusTitle) {
                    statusTitle.textContent = 'Verifying payment...';
                }
                if (statusText) {
                    statusText.textContent = 'Please wait while we confirm your payment with the bank.';
                }
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id || '';
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id || '';
                document.getElementById('razorpay_signature').value = response.razorpay_signature || '';
                document.getElementById('razorpay-verify-form').submit();
            },
            modal: {
                ondismiss() {
                    showRetry('Payment was not completed. You can safely retry when ready.');
                }
            }
        };

        const openPayment = () => {
            if (alertBox) {
                alertBox.style.display = 'none';
            }
            if (statusTitle) {
                statusTitle.textContent = 'Opening secure payment...';
            }
            if (statusText) {
                statusText.textContent = 'Complete the payment in the Razorpay popup. Keep this page open.';
            }
            setLoading(true);

            try {
                if (typeof Razorpay === 'undefined') {
                    showRetry('Payment gateway could not load. Check your connection and try again.');
                    return;
                }

                const checkout = new Razorpay(options);
                checkout.on('payment.failed', function () {
                    showRetry('Payment failed or was declined. No amount has been confirmed by KraftX. Please retry or use another payment method.');
                });
                checkout.open();
            } catch (error) {
                showRetry('The payment popup could not open. Please allow popups and retry.');
            }
        };

        payButton?.addEventListener('click', openPayment);
        retryButton?.addEventListener('click', openPayment);
    </script>
</x-layout>
