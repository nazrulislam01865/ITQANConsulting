<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Initial Admin Account
    |--------------------------------------------------------------------------
    |
    | These values are used only by Database\\Seeders\\AdminUserSeeder.
    | Keep the real email and password in your local/server .env file.
    | Do not hardcode real credentials in committed source files.
    |
    */

    'initial_admin' => [
        'name' => env('ITQAN_ADMIN_NAME', 'ITQAN Administrator'),
        'email' => env('ITQAN_ADMIN_EMAIL'),
        'password' => env('ITQAN_ADMIN_PASSWORD'),
    ],
];
