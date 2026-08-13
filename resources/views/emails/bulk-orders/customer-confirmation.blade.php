@php($brand = config('seo.site_name', config('app.name', 'KraftX')))
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;background:#f4f1ec;font-family:Arial,Helvetica,sans-serif;color:#1f1a16;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;background:#f4f1ec;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#fff;border:1px solid #e7ded3;">
                <tr><td style="padding:28px;background:#111;color:#fff;">
                    <div style="font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#d7cfc5;">{{ $brand }}</div>
                    <h1 style="margin:8px 0 0;font-size:26px;">Request received</h1>
                </td></tr>
                <tr><td style="padding:28px;">
                    <p style="font-size:16px;line-height:25px;">Hi {{ $inquiry->name }},</p>
                    <p style="font-size:15px;line-height:24px;color:#5c524a;">Thank you for contacting us about a bulk order. Our team will review your request and contact you soon.</p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid #eadfd3;">
                        <tr><td style="padding:14px;color:#7b7168;">Product</td><td style="padding:14px;font-weight:700;">{{ $inquiry->product_name }}</td></tr>
                        <tr><td style="padding:14px;color:#7b7168;border-top:1px solid #eadfd3;">Quantity</td><td style="padding:14px;border-top:1px solid #eadfd3;font-weight:700;">{{ number_format($inquiry->quantity) }}</td></tr>
                    </table>
                    <p style="font-size:14px;line-height:23px;color:#6f665e;">You can reply to this email if you need to add anything to your request.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
