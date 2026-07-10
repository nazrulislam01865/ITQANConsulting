# ITQAN resort map: exact template and mobile crop fix

## What changed

- The default map remains the exact `template-map.svg` illustration based on the supplied working prototype.
- Leaflet `L.CRS.Simple` remains the map engine, while places, nodes, curved path geometry and route results continue to load from the database.
- Mobile zoom, zoom-out and fit controls are now placed at the upper-right like the prototype, away from Chrome's dynamic bottom toolbar.
- The Places & pathway button is anchored above the device safe area and no longer gets cropped.
- Portrait phones use an accessible bottom sheet; short landscape phones use a left side sheet.
- `window.visualViewport` is used to keep the Leaflet container synchronized with the real visible Chrome viewport.
- Map and route fitting reserve space for the heading, controls, route card and bottom action.

## Apply on an existing installation

Replace the project files, then run:

```bash
php artisan optimize:clear
```

No database migration is required for this UI-only update. If you need to restore the exact seeded template map and its saved places/pathways, run:

```bash
php artisan db:seed --class=ItqanExternalGuestMapSeeder --force
php artisan optimize:clear
```

Do not run `migrate:fresh` on a production ITQAN installation.
