<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New ITQAN website inquiry</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;color:#17202a;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f4f6f8;padding:28px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:720px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">
                <tr>
                    <td style="padding:26px 30px;background:#07111f;color:#ffffff;">
                        <div style="font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#69d4ff;font-weight:700;">ITQAN Consulting</div>
                        <h1 style="margin:8px 0 0;font-size:24px;line-height:1.3;">New website inquiry</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px;">
                        <p style="margin:0 0 22px;line-height:1.6;color:#4b5563;">A visitor submitted the contact form. You can reply directly to this email to respond to <?php echo e($submission->name); ?>.</p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                            <tr><td style="padding:9px 0;color:#6b7280;width:190px;vertical-align:top;">Name</td><td style="padding:9px 0;font-weight:700;"><?php echo e($submission->name); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Company</td><td style="padding:9px 0;"><?php echo e($submission->company_name ?: '—'); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Email</td><td style="padding:9px 0;"><a href="mailto:<?php echo e($submission->email); ?>" style="color:#087eb4;"><?php echo e($submission->email); ?></a></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Phone / WhatsApp</td><td style="padding:9px 0;"><?php echo e($submission->phone ?: '—'); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Preferred contact</td><td style="padding:9px 0;"><?php echo e($submission->preferred_contact_method ?: '—'); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Business areas</td><td style="padding:9px 0;"><?php echo e(!empty($submission->areas) ? implode(', ', $submission->areas) : '—'); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Support requested</td><td style="padding:9px 0;"><?php echo e(!empty($submission->support_types) ? implode(', ', $submission->support_types) : ($submission->need ?: '—')); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Budget</td><td style="padding:9px 0;"><?php echo e($submission->budget_range ?: '—'); ?></td></tr>
                            <tr><td style="padding:9px 0;color:#6b7280;vertical-align:top;">Submitted</td><td style="padding:9px 0;"><?php echo e(optional($submission->created_at)->format('d M Y, h:i A') ?: '—'); ?></td></tr>
                        </table>

                        <div style="margin-top:24px;padding:18px 20px;border-radius:10px;background:#f8fafc;border:1px solid #e5e7eb;">
                            <div style="margin-bottom:8px;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;font-weight:700;">Message</div>
                            <div style="white-space:pre-wrap;line-height:1.7;color:#1f2937;"><?php echo e($submission->message); ?></div>
                        </div>

                        <p style="margin:22px 0 0;color:#9ca3af;font-size:12px;line-height:1.6;">Submission #<?php echo e($submission->id); ?> · IP <?php echo e($submission->ip_address ?: 'not available'); ?>. A copy remains available in the ITQAN admin contact responses.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/emails/contact-inquiry-received.blade.php ENDPATH**/ ?>