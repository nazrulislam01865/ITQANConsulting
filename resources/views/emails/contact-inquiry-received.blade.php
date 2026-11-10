<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New ITQAN website inquiry</title>
    <style>
        @media only screen and (max-width: 560px) {
            .email-content { padding: 24px 18px !important; }
            .email-header { padding: 25px 18px !important; }
            .detail-label, .detail-value { display: block !important; width: auto !important; padding-bottom: 3px !important; }
            .detail-value { padding-top: 0 !important; padding-bottom: 13px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#f3f6fa;color:#1c2939;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f3f6fa;">
    <tr>
        <td align="center" style="padding:30px 12px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:680px;background:#ffffff;border:1px solid #e1e7ee;border-radius:12px;overflow:hidden;">
                <tr>
                    <td class="email-header" style="padding:30px 34px;background:#0a1828;color:#ffffff;">
                        <p style="margin:0 0 11px;color:#75d9ed;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">ITQAN Consulting</p>
                        <h1 style="margin:0;color:#ffffff;font-size:25px;line-height:1.3;font-weight:700;">New contact inquiry</h1>
                        <p style="margin:9px 0 0;color:#d1dbe5;font-size:14px;line-height:1.6;">A new message has been submitted through the website.</p>
                    </td>
                </tr>
                <tr>
                    <td class="email-content" style="padding:30px 34px;">
                        <p style="margin:0 0 24px;color:#4d5b6c;font-size:14px;line-height:1.7;">You can reply directly to this email to contact <strong style="color:#172b41;">{{ $submission->name }}</strong>.</p>

                        <h2 style="margin:0 0 12px;color:#142c40;font-size:15px;line-height:1.5;">Contact details</h2>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;font-size:14px;line-height:1.5;">
                            <tr><td class="detail-label" width="35%" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Name</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;color:#172b41;font-weight:700;overflow-wrap:anywhere;">{{ $submission->name }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Company</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;overflow-wrap:anywhere;">{{ $submission->company_name ?: '—' }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Email</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;overflow-wrap:anywhere;"><a href="mailto:{{ $submission->email }}" style="color:#087eae;text-decoration:none;">{{ $submission->email }}</a></td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Phone / WhatsApp</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;overflow-wrap:anywhere;">{{ $submission->phone ?: '—' }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Preferred contact</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;">{{ $submission->preferred_contact_method ?: '—' }}</td></tr>
                        </table>

                        <h2 style="margin:27px 0 12px;color:#142c40;font-size:15px;line-height:1.5;">Inquiry details</h2>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;font-size:14px;line-height:1.5;">
                            <tr><td class="detail-label" width="35%" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Business areas</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;overflow-wrap:anywhere;">{{ !empty($submission->areas) ? implode(', ', $submission->areas) : '—' }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Support requested</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;overflow-wrap:anywhere;">{{ !empty($submission->support_types) ? implode(', ', $submission->support_types) : ($submission->need ?: '—') }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Budget</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;">{{ $submission->budget_range ?: '—' }}</td></tr>
                            <tr><td class="detail-label" style="padding:9px 8px 9px 0;border-bottom:1px solid #edf1f5;color:#64758a;vertical-align:top;">Submitted</td><td class="detail-value" style="padding:9px 0;border-bottom:1px solid #edf1f5;">{{ optional($submission->created_at)->format('d M Y, h:i A') ?: '—' }}</td></tr>
                        </table>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:26px;width:100%;border-collapse:collapse;">
                            <tr>
                                <td style="padding:20px;background:#f2f7fa;border-left:4px solid #1684a9;border-radius:4px;">
                                    <p style="margin:0 0 9px;color:#51667b;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Visitor message</p>
                                    <div style="color:#1c2939;font-size:14px;line-height:1.8;overflow-wrap:anywhere;">{!! nl2br(e($submission->message)) !!}</div>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:25px 0 0;color:#8996a4;font-size:12px;line-height:1.6;">Submission #{{ $submission->id }} · IP {{ $submission->ip_address ?: 'not available' }} · This inquiry is also saved in Admin &gt; Contact Responses.</p>
                    </td>
                </tr>
            </table>
            <p style="margin:15px 0 0;color:#9aa5b2;font-size:11px;line-height:1.5;">Website contact form notification · ITQAN Consulting</p>
        </td>
    </tr>
</table>
</body>
</html>
