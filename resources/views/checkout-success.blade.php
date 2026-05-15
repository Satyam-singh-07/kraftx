<x-layout :seo="$seo" title="Order Placed">
    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs justify-content-center">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">Home</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">Order Placed</p>
                </div>
                <h1>Order Placed</h1>
            </div>
        </div>
    </section>

    <section class="flat-spacing pt-40">
        <div class="container">
            <div class="border rounded-20 p-40 text-center mb-30">
                <h4 class="mb-12">Thank you for your order</h4>
                <p class="cl-text-2 mb-0">Order number: <span class="fw-semibold">{{ $order->order_number }}</span></p>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="border rounded-20 p-30">
                        <h5 class="mb-20">Items</h5>
                        <div class="d-grid gap-16">
                            @foreach($order->items as $item)
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <p class="fw-medium mb-4">{{ $item->name }}</p>
                                        <p class="text-caption-01 cl-text-2 mb-0">{{ $item->sku }} · Qty {{ $item->quantity }}</p>
                                    </div>
                                    <p class="fw-medium mb-0">₹{{ number_format($item->total, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="border rounded-20 p-30">
                        <h5 class="mb-20">Summary</h5>
                        <div class="d-grid gap-10">
                            <div class="d-flex justify-content-between">
                                <span>Status</span>
                                <span>{{ \Illuminate\Support\Str::title($order->status) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Payment</span>
                                <span>{{ $order->payment_method }} · {{ \Illuminate\Support\Str::title($order->payment_status) }}</span>
                            </div>
                            <div class="d-flex justify-content-between h5 mb-0 border-top pt-16">
                                <span>Total</span>
                                <span>₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('track.order') }}" class="tf-btn btn-stroke animate-btn w-100 mt-24">Track Order</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
