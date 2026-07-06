# Admin Security, Rate Limit, and Session Timeout Update

## What changed

- Added a global admin security config file: `config/itqan_security.php`.
- Admin login now uses a named Laravel rate limiter: `throttle:admin-login`.
- Admin sessions now have an idle timeout enforced by middleware.
- Admin pages now include a browser-side idle timer that redirects automatically to the login page after the timeout.
- Admin password validation minimum length is configurable.
- Laravel runtime folders include `.gitkeep` files so `storage/framework/views` does not disappear after ZIP/GitHub replacement.

## ENV settings

Add these to `.env` if you want custom values:

```env
ITQAN_ADMIN_SESSION_TIMEOUT_MINUTES=30
ITQAN_ADMIN_LOGIN_MAX_ATTEMPTS=5
ITQAN_ADMIN_LOGIN_DECAY_MINUTES=1
ITQAN_ADMIN_PASSWORD_MIN_LENGTH=12
```

## Behavior

If an admin stays inactive for the configured number of minutes:

1. The browser automatically redirects to `/admin/session-expired`.
2. The current admin session is logged out and invalidated.
3. The admin is redirected to `/admin/login` with an expiry message.

If JavaScript is disabled, the middleware still checks timeout on the next admin request.
