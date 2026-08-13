@php($brand = config('seo.site_name', config('app.name', 'KraftX')))
@php($adminUrl = route('admin.bulk-orders.show', $inquiry))
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#f4f1ec;font-family:Arial,Helvetica,sans-serif;color:#1f1a16;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;background:#f4f1ec;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#fff;border:1px solid #e7ded3;">
                <tr><td style="padding:28px;background:#111;color:#fff;">
                    <div style="font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#d7cfc5;">{{ $brand }} · Admin notification</div>
                    <h1 style="margin:8px 0 0;font-size:26px;">New bulk order inquiry</h1>
                </td></tr>
                <tr><td style="padding:28px;">
                    <p style="margin:0 0 20px;"><a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 18px;background:#111;color:#fff;text-decoration:none;font-weight:700;">View inquiry in admin</a></p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #eadfd3;">
                        <tr><td style="padding:14px;color:#7b7168;">Customer</td><td style="padding:14px;font-weight:700;">{{ $inquiry->name }}</td></tr>
                        <tr><td style="padding:14px;border-top:1px solid #eadfd3;color:#7b7168;">Contact</td><td style="padding:14px;border-top:1px solid #eadfd3;">{{ $inquiry->email }}<br>{{ $inquiry->phone }}</td></tr>
                        <tr><td style="padding:14px;border-top:1px solid #eadfd3;color:#7b7168;">Product</td><td style="padding:14px;border-top:1px solid #eadfd3;font-weight:700;">{{ $inquiry->product_name }}<br><small>SKU: {{ $inquiry->product_sku ?: 'N/A' }}</small></td></tr>
                        <tr><td style="padding:14px;border-top:1px solid #eadfd3;color:#7b7168;">Quantity</td><td style="padding:14px;border-top:1px solid #eadfd3;font-weight:700;">{{ number_format($inquiry->quantity) }}</td></tr>
                        @if($inquiry->message)
                            <tr><td style="padding:14px;border-top:1px solid #eadfd3;color:#7b7168;">Message</td><td style="padding:14px;border-top:1px solid #eadfd3;white-space:pre-line;">{{ $inquiry->message }}</td></tr>
                        @endif
                    </table>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
