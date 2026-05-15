<x-layout :seo="$seo" title="Complete Payment">
    <section class="flat-spacing">
        <div class="container">
            <div class="mx-auto border rounded-20 p-40 text-center" style="max-width: 620px;">
                @if(session('error'))
                    <div class="alert alert-danger mb-20">{{ session('error') }}</div>
                @endif
                <h3 class="mb-12">Complete Payment</h3>
                <p class="cl-text-2 mb-24">Order {{ $order->order_number }} is awaiting payment.</p>
                <div class="d-flex justify-content-between border-top border-bottom py-16 mb-24">
                    <span>Total</span>
                    <strong>₹{{ number_format($order->total_amount, 2) }}</strong>
                </div>
                <button id="pay-now" class="tf-btn type-xl btn-fill animate-btn w-100">Pay Securely</button>
                <a href="{{ route('checkout') }}" class="d-inline-block mt-16 link">Return to checkout</a>
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
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id || '';
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id || '';
                document.getElementById('razorpay_signature').value = response.razorpay_signature || '';
                document.getElementById('razorpay-verify-form').submit();
            },
            modal: {
                ondismiss() {
                    const button = document.getElementById('pay-now');
                    if (button) {
                        button.disabled = false;
                        button.textContent = 'Pay Securely';
                    }
                }
            }
        };

        document.getElementById('pay-now')?.addEventListener('click', function () {
            this.disabled = true;
            this.textContent = 'Opening payment...';
            new Razorpay(options).open();
        });
    </script>
</x-layout>
