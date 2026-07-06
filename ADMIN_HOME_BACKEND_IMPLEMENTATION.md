# ITQAN Admin Backend: Home Page Phase

This phase adds a professional backend foundation and makes the frontend home page dynamic from the database while keeping the template design intact.

## Admin login

URL:

```txt
/admin/login
```

Initial admin credentials come from `.env`. There is no hardcoded default admin email or password in the seeder.

Before running `php artisan migrate --seed`, add your own values to `.env`:

```env
ITQAN_ADMIN_NAME="ITQAN Administrator"
ITQAN_ADMIN_EMAIL=your-admin-email@example.com
ITQAN_ADMIN_PASSWORD=your-strong-admin-password
```

The password must be at least 12 characters. If email/password are missing, the seeder will stop with a clear error message.

## Backend structure

The backend is separated by responsibility:

```txt
app/Http/Controllers/Admin/Auth/AdminLoginController.php
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Admin/SiteSettingController.php
app/Http/Controllers/Admin/HeaderMenuController.php
app/Http/Controllers/Admin/FooterMenuController.php
app/Http/Controllers/Admin/HomePageController.php
app/Http/Controllers/Admin/HomeSectionItemController.php

app/Http/Middleware/EnsureAdminAuthenticated.php

app/Http/Requests/Admin/Auth/AdminLoginRequest.php
app/Http/Requests/Admin/SiteSettingsRequest.php
app/Http/Requests/Admin/MenuItemRequest.php
app/Http/Requests/Admin/FooterMenuItemRequest.php
app/Http/Requests/Admin/HomeSectionRequest.php
app/Http/Requests/Admin/HomeSectionItemRequest.php

app/Services/Admin/AdminAuthService.php
app/Services/Admin/SiteSettingsService.php
app/Services/Admin/NavigationMenuService.php
app/Services/Admin/FooterMenuService.php
app/Services/Admin/HomePageAdminService.php
app/Services/Admin/ImageUploadService.php

app/Services/Frontend/ItqanFrontendContentService.php
```

## Database tables added

```txt
site_settings
navigation_menu_items
footer_menu_items
home_sections
home_section_items
```

The `users` table now has:

```txt
is_admin
is_active
last_login_at
```

## Dynamic home sections

The following home sections are editable separately:

```txt
Hero Section
Founder Section
Who We Are Section
Why ITQAN Exists Section
Services Preview Section
Our Way of Working Section
Testimonials Section
Works Preview Section
Final CTA Section
```

## Header and footer control

Header menu is controlled from:

```txt
/admin/header-menu
```

Footer menu is controlled from:

```txt
/admin/footer-menu
```

Logo and global text are controlled from:

```txt
/admin/site-settings
```

## Setup commands

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link

# Add ITQAN_ADMIN_EMAIL and ITQAN_ADMIN_PASSWORD to .env before this command
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

If you already migrated before, use:

```bash
php artisan migrate --seed
```

For a fresh local reset:

```bash
php artisan migrate:fresh --seed
```

## Notes

- The frontend design CSS remains in `public/assets/css/itqan-template.css`.
- Admin CSS is separated in `public/assets/css/admin.css`.
- The frontend falls back to `config/itqan.php` if the database tables are not migrated yet.
- Only the home page is database-driven in this phase, as requested.

## Admin Home Submenu Update

The admin sidebar now treats **Home Page** as a parent menu. The Home overview and every individual home page section editor appear as submenu links under Home Page.

Implementation files:

```txt
app/Services/Admin/AdminNavigationService.php
app/Providers/AppServiceProvider.php
resources/views/admin/partials/sidebar.blade.php
public/assets/css/admin.css
```

The sidebar section list is loaded through `AdminNavigationService`, not directly from random Blade logic. The service is migration-safe and returns an empty collection if the `home_sections` table is not available yet.

## 2026-07-06 Hero admin sequence update

- Home Page sidebar parent is now a real collapsible button with persistent open/closed state.
- Hero editor is arranged in the same frontend sequence: label, title, description, chips, buttons, social label/links, marquee.
- Item type fields are dropdowns using a controlled allow-list.
- Hero social media links are editable as items with platform, label, and URL fields.
- Marquee items now use simple plain-text inputs. Default marquee text was simplified to be easier for admins to edit.

## Founder Section Admin Update

The Founder Section editor now follows the frontend sequence. The left visual can be replaced with an uploaded image, the body copy is managed from one textarea directly after the headline, and the separate paragraph item manager is hidden for this section.

Updated behavior:

- Left side image upload is stored in `settings.image_path`.
- Founder body text is stored in the `home_sections.description` field.
- The frontend splits the textarea content by line breaks into paragraphs, preserving the original paragraph design.
- Existing paragraph items are ignored as a fallback only when `description` is empty.
- Seeder now stores the default founder text in `description` and removes old paragraph items for the Founder Section.
