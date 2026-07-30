@php
    $brand = config('seo.site_name', config('app.name', 'KraftX'));
    $logo = asset('assets/images/logo/logo.png');
    $adminOrderUrl = route('admin.orders.show', $order);
    $shipping = $order->shipping_address_data ?: [
        'address' => $order->shipping_address,
        'city' => $order->shipping_city,
        'state' => $order->shipping_state,
        'pincode' => $order->shipping_pincode,
        'country' => $order->shipping_country,
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>New order placed</title>
</head>
<body style="margin:0; padding:0; background:#f4f1ec; font-family:Arial, Helvetica, sans-serif; color:#1f1a16;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f1ec; margin:0; padding:0;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px; width:100%; background:#ffffff; border:1px solid #e7ded3;">
                    <tr>
                        <td style="background:#111111; padding:26px 28px; text-align:left;">
                            <img src="{{ $logo }}" width="140" alt="{{ $brand }}" style="display:block; border:0; max-width:140px; height:auto; margin-bottom:18px;">
                            <div style="font-size:13px; line-height:20px; color:#d7cfc5; letter-spacing:.4px; text-transform:uppercase;">Admin notification</div>
                            <h1 style="margin:8px 0 0; font-size:28px; line-height:36px; font-weight:700; color:#ffffff;">New order placed: {{ $order->order_number }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 18px; font-size:16px; line-height:26px; color:#4f463f;">
                                A new order has been confirmed on {{ $brand }}. Review the order details and prepare fulfillment.
                            </p>

                            <p style="margin:0 0 24px;">
                                <a href="{{ $adminOrderUrl }}" style="display:inline-block; background:#111111; color:#ffffff; text-decoration:none; padding:12px 18px; font-size:14px; font-weight:700;">View order in admin</a>
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:22px 0; border:1px solid #eadfd3;">
                                <tr>
                                    <td width="50%" style="padding:16px; border-bottom:1px solid #eadfd3; border-right:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Customer</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->customer_name }}</div>
                                        <div style="font-size:13px; line-height:20px; color:#6f665e;">{{ $order->customer_email }}<br>{{ $order->customer_phone }}</div>
                                    </td>
                                    <td width="50%" style="padding:16px; border-bottom:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Order date</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding:16px; border-right:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Payment</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->payment_method === 'COD' ? 'Cash on Delivery' : 'Online payment' }}</div>
                                        <div style="font-size:13px; line-height:20px; color:#6f665e;">{{ \Illuminate\Support\Str::title($order->payment_status) }}</div>
                                    </td>
                                    <td width="50%" style="padding:16px;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Total</div>
                                        <div style="font-size:18px; line-height:26px; font-weight:700;">₹{{ number_format($order->total_amount, 2) }}</div>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:28px 0 12px; font-size:20px; line-height:28px;">Items</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding:12px 0; border-bottom:1px solid #eee6dc;">
                                            <div style="font-size:15px; line-height:22px; font-weight:700;">{{ $item->name }}</div>
                                            <div style="font-size:13px; line-height:20px; color:#7b7168;">
                                                @if($item->variant)
                                                    {{ collect([$item->variant->color, $item->variant->size])->filter()->implode(' / ') }} ·
                                                @endif
                                                {{ $item->sku }} · Qty {{ $item->quantity }}
                                            </div>
                                        </td>
                                        <td align="right" style="padding:12px 0; border-bottom:1px solid #eee6dc; font-size:15px; font-weight:700; white-space:nowrap;">
                                            ₹{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:18px 0 26px;">
                                <tr>
                                    <td style="padding:7px 0; color:#6f665e;">Subtotal</td>
                                    <td align="right" style="padding:7px 0; font-weight:700;">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:7px 0; color:#6f665e;">Shipping</td>
                                    <td align="right" style="padding:7px 0; font-weight:700;">{{ (float) $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount, 2) : 'Free' }}</td>
                                </tr>
                                @if((float) $order->payment_fee_amount > 0)
                                    <tr>
                                        <td style="padding:7px 0; color:#6f665e;">Cash Handling Fee</td>
                                        <td align="right" style="padding:7px 0; font-weight:700;">₹{{ number_format($order->payment_fee_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if((float) $order->payment_discount_amount > 0)
                                    <tr>
                                        <td style="padding:7px 0; color:#2f7a3e;">Prepaid Savings</td>
                                        <td align="right" style="padding:7px 0; font-weight:700; color:#2f7a3e;">-₹{{ number_format($order->payment_discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:14px 0 0; border-top:1px solid #eadfd3; font-size:18px; font-weight:700;">Total</td>
                                    <td align="right" style="padding:14px 0 0; border-top:1px solid #eadfd3; font-size:18px; font-weight:700;">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:16px; background:#fbfaf8; border:1px solid #eadfd3;">
                                        <h3 style="margin:0 0 8px; font-size:16px;">Shipping address</h3>
                                        <p style="margin:0; font-size:14px; line-height:23px; color:#5c524a;">
                                            {{ $shipping['address'] ?? $order->shipping_address }}<br>
                                            {{ $shipping['city'] ?? $order->shipping_city }}, {{ $shipping['state'] ?? $order->shipping_state }} {{ $shipping['pincode'] ?? $order->shipping_pincode }}<br>
                                            {{ $shipping['country'] ?? $order->shipping_country }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
