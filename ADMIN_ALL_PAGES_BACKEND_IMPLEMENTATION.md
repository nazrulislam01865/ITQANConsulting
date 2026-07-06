# ITQAN Admin Backend — All Frontend Pages

This update adds section-by-section admin control for the remaining frontend pages while keeping the frontend Blade/CSS structure intact.

## Admin menu structure

- Home Page
  - Existing separated Home sections
- About Page
  - Hero Section
  - Our Story Section
  - What We Believe Section
  - Mission & Vision Section
  - CTA Section
- Services Page
  - Hero Section
  - Service Areas Section
  - Common Questions Section
  - CTA Section
- Works Page
  - Hero Section
  - Work Listing Section
- Catalog Page
  - Hero Section
  - Catalog Viewer Section
- Contact Page
  - Hero Section
  - Contact Form Section
  - CTA Section

## Backend structure

- Controllers: `PageContentController`, `PageSectionItemController`
- Requests: `PageSectionRequest`, `PageSectionItemRequest`
- Services: `PageAdminService`, `AdminNavigationService`
- Models: `PageSection`, `PageSectionItem`
- Migrations: `page_sections`, `page_section_items`
- Views: `resources/views/admin/pages/...`

## Frontend data flow

The frontend keeps the same design files. Dynamic content is loaded through:

`App\Services\Frontend\ItqanFrontendContentService`

If the new database tables do not exist yet, the site safely falls back to `config/itqan.php`.

## Install/update commands

After replacing files:

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=ItqanWebsiteSeeder
php artisan view:clear
```

If this is a fresh setup and you also need the initial admin account, make sure `.env` contains:

```env
ITQAN_ADMIN_EMAIL=your-email@example.com
ITQAN_ADMIN_PASSWORD=your-strong-password
```

Then use:

```bash
php artisan migrate --seed
```
