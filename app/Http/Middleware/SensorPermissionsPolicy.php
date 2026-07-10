<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SensorPermissionsPolicy
{
    /**
     * Allow this first-party resort map to use the phone sensors required by
     * Chrome navigation while denying those sensors to unrelated origins.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($request->is('external-guest-map*')) {
            $response->headers->set(
                'Permissions-Policy',
                'accelerometer=(self), gyroscope=(self), magnetometer=(self), geolocation=(self), screen-wake-lock=(self)'
            );
        }

        return $response;
    }
}
