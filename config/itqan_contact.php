<?php

return [
    'recipient_email' => env('CONTACT_INQUIRY_RECIPIENT', 'aminul@itqanconsulting.com'),

    'captcha' => [
        'enabled' => filter_var(env('CONTACT_CAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOL),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => env('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify'),
        'timeout_seconds' => max(1, (int) env('RECAPTCHA_TIMEOUT_SECONDS', 5)),
        'expected_hostname' => env('RECAPTCHA_EXPECTED_HOSTNAME'),
    ],
];
