<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Session Timeout
    |--------------------------------------------------------------------------
    |
    | Number of idle minutes before an administrator is automatically signed out.
    | The middleware enforces this on every admin request, and the admin layout
    | redirects the browser to the login page automatically after the same time.
    |
    */
    'admin_session_timeout_minutes' => (int) env('ITQAN_ADMIN_SESSION_TIMEOUT_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Admin Login Rate Limit
    |--------------------------------------------------------------------------
    */
    'admin_login_max_attempts' => (int) env('ITQAN_ADMIN_LOGIN_MAX_ATTEMPTS', 5),
    'admin_login_decay_minutes' => (int) env('ITQAN_ADMIN_LOGIN_DECAY_MINUTES', 1),

    /*
    |--------------------------------------------------------------------------
    | Admin Password Rule
    |--------------------------------------------------------------------------
    */
    'admin_password_min_length' => (int) env('ITQAN_ADMIN_PASSWORD_MIN_LENGTH', 12),
];
