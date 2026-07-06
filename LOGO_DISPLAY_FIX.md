# Logo display fix

The logo was not showing because `SiteSetting::logoUrl()` used `Storage::disk('public')->url(...)`.
That depends on `APP_URL`, so on local development it can generate a URL like `http://localhost/storage/...` while the site is opened at `http://127.0.0.1:8000`.

Fixes applied:

- Logo URL now uses `asset('storage/...')`, so it follows the current request host/port.
- If the saved logo file is missing, the admin screen shows a clear warning instead of a broken image.
- Laravel's built-in local storage route was disabled to avoid conflict with `/storage/...`.
- A public storage fallback route now serves files from `storage/app/public` if the `public/storage` symlink is missing.

Still recommended after deployment/replacement:

```bash
php artisan storage:link
php artisan optimize:clear
php artisan view:clear
```
