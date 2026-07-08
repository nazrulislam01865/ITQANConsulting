# Maintenance Page Update

## What was added

A custom Laravel maintenance/error page was added at:

- `resources/views/errors/503.blade.php`

Laravel automatically uses this page when the application is in maintenance mode and returns an HTTP 503 response.

## How to show the maintenance page

Run this from the project root on the server:

```bash
php artisan down
```

## Recommended pre-rendered maintenance mode

For a safer deployment maintenance screen, use Laravel's rendered maintenance option:

```bash
php artisan down --render="errors::503"
```

## How to bring the website back online

```bash
php artisan up
```

## Notes

- The page is self-contained with inline CSS.
- It does not depend on the database, frontend Vite build, or admin settings.
- It keeps the current ITQAN visual style.
- It uses a clean static 503 page without flashy redirects.
