@component('account.partials.shell', ['seo' => $seo])
    <style>
        .account-dashboard-recent {
            overflow-x: auto;
        }

        .account-dashboard-recent .table-my_recent {
            min-width: 720px;
            width: 100%;
            white-space: normal;
        }

        .account-dashboard-recent .table-my_recent tr {
            display: grid;
            grid-template-columns: minmax(130px, 0.9fr) minmax(240px, 2fr) minmax(100px, 0.7fr) minmax(120px, 0.8fr);
            align-items: center;
            gap: 18px;
        }

        .account-dashboard-recent .table-my_recent th,
        .account-dashboard-recent .table-my_recent td {
            width: auto !important;
            min-width: 0;
        }

        .account-dashboard-recent .tb-order_product,
        .account-dashboard-recent .infor-prd {
            min-width: 0;
        }

        .account-dashboard-recent .tb-order_product .prd_name {
            max-width: 100%;
            word-break: break-word;
        }

        .account-dashboard-recent .tb-order_price,
        .account-dashboard-recent .tb-order_status {
            white-space: nowrap;
        }

        .account-dashboard-recent .tb-order_status.stt-processing {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
        }

        @media (max-width: 767px) {
            .account-dashboard-recent .table-my_recent {
                min-width: 640px;
            }
        }
    </style>

    <h4 class="account-title">Dashboard</h4>
    <div class="acount-order_stats">
        <div class="row g-3">
            <div class="col-sm-4">
                <div class="order-box">
                    <div class="order_info">
                        <p class="info__label cl-text-2">Awaiting Pickup</p>
                        <h5 class="info__count type-semibold">{{ $stats['pending'] }}</h5>
                    </div>
                    <div class="order_icon">
                        <i class="icon icon-HourglassMedium"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="order-box">
                    <div class="order_info">
                        <p class="info__label cl-text-2">Cancelled Orders</p>
                        <h5 class="info__count type-semibold">{{ $stats['cancelled'] }}</h5>
                    </div>
                    <div class="order_icon">
                        <i class="icon icon-ReceiptX"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="order-box">
                    <div class="order_info">
                        <p class="info__label cl-text-2">Total Number of Orders</p>
                        <h5 class="info__count type-semibold">{{ $stats['total'] }}</h5>
                    </div>
                    <div class="order_icon">
                        <i class="icon icon-Package"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="account-my_recent">
        <h6 class="title-case">Recent Orders</h6>
        <div class="account-dashboard-recent">
            <table class="table-my_recent">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Products</th>
                        <th>Pricing</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="tb-order-item">
                            <td class="tb-order_code fw-medium">{{ $order->order_number }}</td>
                            <td>
                                <div class="tb-order_product">
                                    <div class="infor-prd">
                                        <a href="{{ route('account.orders') }}" class="prd_name link fw-medium lh-24">
                                            {{ $order->items->first()?->name ?? 'Order items' }}
                                        </a>
                                        <p class="prd_type cl-text-2 text-caption-01">
                                            {{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="tb-order_price fw-medium">₹{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <div class="tb-order_status text-label stt-{{ str_replace(' ', '-', strtolower($order->status)) }}">
                                    {{ \Illuminate\Support\Str::title($order->status) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center cl-text-2 py-4">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
