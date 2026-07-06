# ITQAN Consulting Frontend Implementation

This Laravel frontend keeps the uploaded prototype design intact and converts it into reusable, config-driven Blade views.

## What is implemented

- Real Laravel routes for Home, About, Services, Works, Catalog, and Contact.
- Reusable layout, header, footer, hero, CTA, social links, section headings, and work card partials.
- All editable frontend content centralized in `config/itqan.php` for easy backend replacement later.
- Original prototype CSS moved into `resources/css/app.css` without redesigning the UI.
- Original interaction behavior moved into `resources/js/app.js`:
  - mobile menu
  - motion toggle
  - reveal animation
  - FAQ accordion
  - works filtering
  - catalog flipbook
  - thumbnail/fullscreen controls
  - touch swipe on catalog
- Vite-compatible assets are kept in `resources/`.
- A temporary `public/build` manifest/assets are included so the site can load before running a fresh Vite build.

## Main files

```txt
app/Http/Controllers/Frontend/PageController.php
config/itqan.php
routes/web.php
resources/views/frontend/layouts/app.blade.php
resources/views/frontend/partials/*.blade.php
resources/views/frontend/pages/*.blade.php
resources/css/app.css
resources/js/app.js
```

## Local setup

```bash
cp .env.example .env
php artisan key:generate
composer install
npm install
npm run build
php artisan serve
```

Open:

```txt
http://127.0.0.1:8000
```

## Later backend plan

When the admin panel is created, replace the arrays in `config/itqan.php` with database records while keeping the same view variable structure. This avoids redesigning the frontend.
