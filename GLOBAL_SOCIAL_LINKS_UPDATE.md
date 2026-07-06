# Global Social Links Update

Implemented a global Social Links admin module.

## Admin

New route/menu:

- `/admin/social-links`

Admin can manage:

- Platform
- Label
- URL
- Optional SVG icon/path
- Active/inactive status

## Frontend reuse

The same active social links are now reused by:

- Home hero social icons
- Contact page side note social icons
- Footer social icons
- Any future social icon section that includes `frontend.partials.social-links`

The Home Hero editor now only controls the social label text. The actual icons and links come from the global Social Links module.

## Migration

Added:

- `social_links` table

Run:

```bash
php artisan migrate
php artisan db:seed --class=ItqanWebsiteSeeder
```

## Admin submenu flash fix

Admin sidebar/menu rendering is hidden until persisted submenu state is restored. This prevents flicker/flash when navigating between menu/submenu pages.
