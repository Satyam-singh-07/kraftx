@php
    $brand = config('seo.site_name', config('app.name', 'KraftX'));
    $supportEmail = config('seo.support_email', 'thekraftxofficial@gmail.com');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Your KraftX verification code</title>
</head>
<body style="margin:0; padding:0; background:#f5f2ec; font-family:Arial, Helvetica, sans-serif; color:#201a16;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0; padding:0; background:#f5f2ec;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:560px; background:#ffffff; border:1px solid #e8dfd4;">
                    <tr>
                        <td style="padding:24px 26px; background:#111111;">
                            <div style="font-size:24px; line-height:30px; font-weight:700; color:#ffffff; letter-spacing:.2px;">{{ $brand }}</div>
                            <div style="margin-top:6px; font-size:13px; line-height:19px; color:#d7cfc5;">Secure account verification</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 26px 10px;">
                            <h1 style="margin:0; font-size:24px; line-height:32px; font-weight:700; color:#201a16;">Verify your email address</h1>
                            <p style="margin:12px 0 0; font-size:15px; line-height:24px; color:#5f554d;">
                                Use the one-time password below to continue signing in to your KraftX account.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 26px 8px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fbfaf8; border:1px solid #e8dfd4;">
                                <tr>
                                    <td align="center" style="padding:26px 18px;">
                                        <div style="font-size:12px; line-height:18px; color:#7a7067; text-transform:uppercase; letter-spacing:1px;">Your OTP code</div>
                                        <div style="margin-top:10px; font-size:40px; line-height:48px; font-weight:700; color:#111111; letter-spacing:8px;">{{ $otp }}</div>
                                        <div style="margin-top:14px; font-size:14px; line-height:22px; color:#6d6259;">This code expires in 10 minutes.</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 26px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fff7ed; border:1px solid #f0d8b8;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0; font-size:14px; line-height:22px; color:#5c4330;">
                                            <strong style="color:#25170c;">Do not share this OTP.</strong> KraftX will never ask for your OTP by phone, message, or email. If you did not request this code, you can safely ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px 26px; background:#f8f5f0; border-top:1px solid #e8dfd4;">
                            <p style="margin:0 0 8px; font-size:13px; line-height:20px; color:#6f665e;">
                                Need help? Contact <a href="mailto:{{ $supportEmail }}" style="color:#201a16; text-decoration:underline;">{{ $supportEmail }}</a>.
                            </p>
                            <p style="margin:0; font-size:12px; line-height:19px; color:#8a8076;">&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
