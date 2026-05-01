@component('account.partials.shell', ['seo' => $seo])
    <h4 class="account-title">Your Orders</h4>
    <div class="account-my_order">
        <div class="my-order_list d-grid gap-24">
            @forelse ($orders as $order)
                <div class="wg-my-order">
                    <div class="order-heading">
                        <div class="order_number fw-medium">
                            Order Number:
                            <span class="number-code fw-semibold">{{ $order->order_number }}</span>
                        </div>
                        <div class="order_status fw-medium">
                            Order Status:
                            <div class="tb-order_status text-label stt-{{ str_replace(' ', '-', strtolower($order->status)) }}">
                                {{ \Illuminate\Support\Str::title($order->status) }}
                            </div>
                        </div>
                    </div>
                    <div class="order-content">
                        @forelse ($order->items as $item)
                            <div class="order_prd_item">
                                <div class="prd__info">
                                    <p class="name fw-medium">{{ $item->name }}</p>
                                    <p class="type cl-text-2">{{ $item->sku }}</p>
                                </div>
                                <div class="prd__price fw-medium">
                                    <span class="quantity">{{ $item->quantity }}</span>
                                    x
                                    <span class="price">₹{{ number_format($item->price, 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="cl-text-2 mb-0">No items found for this order.</p>
                        @endforelse
                        <div class="group-btn">
                            <span class="action-order tf-btn small btn-stroke">₹{{ number_format($order->total_amount, 2) }}</span>
                            <span class="action-order tf-btn small btn-stroke">{{ \Illuminate\Support\Str::title($order->payment_status) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center border rounded-20 p-40">
                    <h6 class="mb-8">No orders yet</h6>
                    <p class="cl-text-2 mb-0">Your orders will appear here after checkout.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endcomponent
