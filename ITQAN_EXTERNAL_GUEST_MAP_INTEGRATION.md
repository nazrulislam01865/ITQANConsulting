# Itqan External Guest Map Integration

This map has been added as an isolated admin test route. It does not add any sidebar/menu/header link.

## Test URL

After admin login, open:

```text
/admin/external-guest-map
```

## Added route group

All routes are under:

```text
/admin/external-guest-map
```

API routes are also under the same prefix:

```text
/admin/external-guest-map/api/data
/admin/external-guest-map/api/route
/admin/external-guest-map/api/location
/admin/external-guest-map/api/route-log/{routeLog}/finish
```

## Separate database tables

The map uses only these external test tables:

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

It does not use or modify the existing Itqan content/admin tables.

## Added files/directories

```text
app/Http/Controllers/External/ItqanGuestMapController.php
app/Models/ExternalGuestMap/
database/migrations/2026_07_09_170000_create_ext_guest_map_tables.php
database/seeders/ItqanExternalGuestMapSeeder.php
resources/views/external/itqan-guest-map/index.blade.php
public/assets/itqan-external-guest-map/
public/vendor/itqan-guest-map-leaflet/
```

## Setup commands

```bash
php artisan migrate
php artisan db:seed --class=ItqanExternalGuestMapSeeder --force
php artisan optimize:clear
```

## Removal

Remove the route group from `routes/web.php`, delete the added files/directories above, then rollback/drop the `ext_guest_map_*` tables.
