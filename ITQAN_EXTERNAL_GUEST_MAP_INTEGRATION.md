# ITQAN Palace Guest Map Integration

The Palace navigation update is merged into the ITQAN Laravel project as an isolated public module. It does not replace or modify the normal ITQAN website pages, menus, content-management tables, or admin authentication.

## Public map URL

```text
/external-guest-map
```

The map uses first-party, deployment-relative API and asset URLs, so it can run on a cloud domain or under a subdirectory without relying on the standalone project's `/api/guest-map/*` paths.

## Included navigation update

- Changeable starting point using the origin selector, place popup, or “Choose start on map” mode
- Real-time DeviceMotion step detection
- DeviceOrientation heading and phone-direction alignment
- Junction-aware graph walking and branch selection
- Route recalculation after selecting a different road at a junction
- Remaining-route rendering, turn maneuvers, and spoken guidance
- GPS fallback when DeviceMotion is unavailable
- Chrome mobile safe-area/touch behavior
- First-party `Permissions-Policy` response header
- Deployment-relative endpoints and static assets

## Routes

```text
GET  /external-guest-map
GET  /external-guest-map/api/data
GET  /external-guest-map/api/route
POST /external-guest-map/api/location
POST /external-guest-map/api/navigation/finish
```

## Separate database tables

```text
ext_guest_map_settings
ext_guest_map_categories
ext_guest_map_nodes
ext_guest_map_places
ext_guest_map_edges
ext_guest_map_qr_points
ext_guest_map_route_logs
ext_guest_map_location_logs
```

## Cloud setup

Run these commands from the project root after configuring `.env`:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=ItqanExternalGuestMapSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

The web server document root must point to `public/`. Phone motion/orientation APIs require the final browser URL to use HTTPS.
