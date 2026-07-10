# ITQAN Resort Guest Map Replacement

The previous sensor/navigation guest map has been replaced by the latest mobile-first curved-path map.

## Public URL

- `/external-guest-map`

## Admin URL

- `/admin/map` (protected by the existing ITQAN admin session middleware)

## Included

- Leaflet `L.CRS.Simple` illustrated-map engine
- responsive place/pathway bottom sheet
- working zoom, fit-map, fullscreen, search, start/destination and route display
- curved road rendering with exact shared vertex endpoints
- database-backed map settings, places, route vertices and path control points
- map image upload and coordinate/path editing in Admin

## Removed

- GPS, compass, motion/step tracking, live navigation, route logs and location logs
- sensor permission middleware and movement engine JavaScript

## Deployment

For an existing ITQAN database:

```bash
php artisan migrate --force
php artisan db:seed --class=ItqanExternalGuestMapSeeder --force
php artisan optimize:clear
```

The seeder intentionally replaces only the external guest-map records. It does not reset ITQAN website content.

For a new local installation:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan optimize:clear
php artisan serve
```
