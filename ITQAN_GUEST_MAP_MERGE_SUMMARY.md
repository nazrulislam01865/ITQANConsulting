# ITQAN Guest Map Merge Summary

The latest Palace navigation update from `Archive.zip` has been merged into the existing isolated guest-map module from the ITQAN project.

## Main changes

- Replaced the older GPS-only frontend with the updated device-motion, heading, junction, graph-walker, and route-follow engines.
- Kept the guest map inside ITQAN's existing `ExternalGuestMap` model namespace and `ext_guest_map_*` database tables.
- Added server-generated deployment-relative API URLs instead of standalone hardcoded `/api/guest-map/*` endpoints.
- Added mobile Chrome metadata, touch/safe-area handling, sensor status, live turn guidance, voice guidance, follow mode, and rerouting UI.
- Replaced the fixed Kids Zone origin with a fully changeable starting-point selector and map-picking flow.
- Added navigation maneuvers and georeference metadata to the Laravel controller response.
- Added the map-north database setting migration and seeder support.
- Added a first-party sensor `Permissions-Policy` header only for the guest-map routes.
- Kept a legacy route-log finish endpoint for compatibility while using the new body-based finish endpoint in the frontend.
- Added map engine tests and cloud deployment documentation.

## Validation completed

- Motion engine tests passed.
- Route-follow engine tests passed.
- Graph-walker tests passed for all 20 nodes and 24 edges.
- Controller maneuver-generation test passed.
- All 101 PHP files passed syntax checking.
- All JavaScript engine files passed syntax checking.
- Every DOM element required by the updated JavaScript exists in the Blade view.
- All referenced local map and Leaflet assets exist.
- Laravel lists the public guest-map routes correctly.

Full PHPUnit/database HTTP execution requires the deployment PHP environment to include the PDO database driver and the standard DOM/XML/Mbstring extensions listed in the cloud deployment guide.
