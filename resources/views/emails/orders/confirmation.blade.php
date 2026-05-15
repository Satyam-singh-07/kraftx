@php
    $brand = config('seo.site_name', config('app.name', 'KraftX'));
    $supportEmail = config('seo.support_email', 'thekraftxofficial@gmail.com');
    $supportPhone = config('seo.support_phone');
    $logo = asset('assets/images/logo/logo.png');
    $billing = $order->billing_address_data ?: $order->shipping_address_data;
    $shipping = $order->shipping_address_data ?: [
        'address' => $order->shipping_address,
        'city' => $order->shipping_city,
        'state' => $order->shipping_state,
        'pincode' => $order->shipping_pincode,
        'country' => $order->shipping_country,
    ];
    $socialLinks = collect(config('seo.social', []))->filter();
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Order confirmed</title>
</head>
<body style="margin:0; padding:0; background:#f4f1ec; font-family:Arial, Helvetica, sans-serif; color:#1f1a16;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f1ec; margin:0; padding:0;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px; width:100%; background:#ffffff; border:1px solid #e7ded3;">
                    <tr>
                        <td style="background:#111111; padding:26px 28px; text-align:left;">
                            <img src="{{ $logo }}" width="140" alt="{{ $brand }}" style="display:block; border:0; max-width:140px; height:auto; margin-bottom:18px;">
                            <div style="font-size:13px; line-height:20px; color:#d7cfc5; letter-spacing:.4px; text-transform:uppercase;">Order confirmed</div>
                            <h1 style="margin:8px 0 0; font-size:28px; line-height:36px; font-weight:700; color:#ffffff;">Thank you for your order, {{ $order->customer_name }}.</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:26px; color:#4f463f;">
                                We have received your order and will prepare it with care. This email is your confirmation for <strong style="color:#1f1a16;">{{ $order->order_number }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:22px 0; border:1px solid #eadfd3;">
                                <tr>
                                    <td width="50%" style="padding:16px; border-bottom:1px solid #eadfd3; border-right:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Order date</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td width="50%" style="padding:16px; border-bottom:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Payment method</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->payment_method === 'COD' ? 'Cash on Delivery' : 'Online payment' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding:16px; border-right:1px solid #eadfd3;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Payment status</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ \Illuminate\Support\Str::title($order->payment_status) }}</div>
                                    </td>
                                    <td width="50%" style="padding:16px;">
                                        <div style="font-size:12px; color:#7b7168; text-transform:uppercase;">Order number</div>
                                        <div style="font-size:15px; line-height:23px; font-weight:700;">{{ $order->order_number }}</div>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:28px 0 12px; font-size:20px; line-height:28px;">Items ordered</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                @foreach($order->items as $item)
                                    @php
                                        $imagePath = $item->product?->images?->first()?->image_path;
                                        $imageUrl = $imagePath ? asset('storage/' . $imagePath) : asset('assets/images/product/product-placeholder.jpg');
                                    @endphp
                                    <tr>
                                        <td style="padding:14px 0; border-bottom:1px solid #eee6dc;" width="76">
                                            <img src="{{ $imageUrl }}" width="64" height="78" alt="{{ $item->name }}" style="display:block; width:64px; height:78px; object-fit:cover; border:0; background:#f4f1ec;">
                                        </td>
                                        <td style="padding:14px 10px; border-bottom:1px solid #eee6dc;">
                                            <div style="font-size:15px; line-height:22px; font-weight:700;">{{ $item->name }}</div>
                                            <div style="font-size:13px; line-height:20px; color:#7b7168;">{{ $item->sku }} · Qty {{ $item->quantity }}</div>
                                        </td>
                                        <td align="right" style="padding:14px 0; border-bottom:1px solid #eee6dc; font-size:15px; font-weight:700; white-space:nowrap;">
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
                                <tr>
                                    <td style="padding:7px 0; color:#6f665e;">Discount</td>
                                    <td align="right" style="padding:7px 0; font-weight:700;">{{ (float) $order->discount_amount > 0 ? '-₹' . number_format($order->discount_amount, 2) : '₹0.00' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0 0; border-top:1px solid #eadfd3; font-size:18px; font-weight:700;">Total {{ $order->payment_status === 'paid' ? 'paid' : 'payable' }}</td>
                                    <td align="right" style="padding:14px 0 0; border-top:1px solid #eadfd3; font-size:18px; font-weight:700;">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td valign="top" width="50%" style="padding:16px; background:#fbfaf8; border:1px solid #eadfd3;">
                                        <h3 style="margin:0 0 8px; font-size:16px;">Shipping address</h3>
                                        <p style="margin:0; font-size:14px; line-height:23px; color:#5c524a;">
                                            {{ $shipping['address'] ?? $order->shipping_address }}<br>
                                            {{ $shipping['city'] ?? $order->shipping_city }}, {{ $shipping['state'] ?? $order->shipping_state }} {{ $shipping['pincode'] ?? $order->shipping_pincode }}<br>
                                            {{ $shipping['country'] ?? $order->shipping_country }}<br>
                                            {{ $order->customer_phone }}<br>
                                            {{ $order->customer_email }}
                                        </p>
                                    </td>
                                    <td width="12" style="font-size:0; line-height:0;">&nbsp;</td>
                                    <td valign="top" width="50%" style="padding:16px; background:#fbfaf8; border:1px solid #eadfd3;">
                                        <h3 style="margin:0 0 8px; font-size:16px;">Billing address</h3>
                                        <p style="margin:0; font-size:14px; line-height:23px; color:#5c524a;">
                                            {{ $billing['address'] ?? $order->shipping_address }}<br>
                                            {{ $billing['city'] ?? $order->shipping_city }}, {{ $billing['state'] ?? $order->shipping_state }} {{ $billing['pincode'] ?? $order->shipping_pincode }}<br>
                                            {{ $billing['country'] ?? $order->shipping_country }}<br>
                                            {{ $order->customer_phone }}<br>
                                            {{ $order->customer_email }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:26px; background:#111111;">
                                <tr>
                                    <td style="padding:20px; color:#ffffff;">
                                        <h3 style="margin:0 0 8px; font-size:18px; color:#ffffff;">What happens next?</h3>
                                        <p style="margin:0; font-size:14px; line-height:23px; color:#ded8d0;">
                                            We will review and process your order. You will receive tracking details once dispatch is ready.
                                            For support, email <a href="mailto:{{ $supportEmail }}" style="color:#ffffff; text-decoration:underline;">{{ $supportEmail }}</a>@if($supportPhone) or call {{ $supportPhone }}@endif.
                                            We usually respond within 1 business day.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:22px 28px; background:#f8f5f0; border-top:1px solid #eadfd3;">
                            @if($socialLinks->isNotEmpty())
                                <p style="margin:0 0 8px; font-size:13px; color:#6f665e;">
                                    @foreach($socialLinks as $name => $url)
                                        <a href="{{ $url }}" style="color:#1f1a16; text-decoration:underline; margin:0 6px;">{{ \Illuminate\Support\Str::title($name) }}</a>
                                    @endforeach
                                </p>
                            @endif
                            <p style="margin:0; font-size:12px; line-height:20px; color:#7b7168;">© {{ date('Y') }} {{ $brand }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
