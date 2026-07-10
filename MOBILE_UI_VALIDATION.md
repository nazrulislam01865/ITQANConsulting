# Mobile UI validation

The guest map was exercised in a headless Chromium touch context with the exact database-backed Leaflet page and the supplied template SVG.

Validated viewports:

- 1000 × 536 landscape/short viewport
- 390 × 844 portrait phone
- 360 × 640 small portrait phone

Checks passed:

- Places & pathway button stayed fully inside the visible viewport.
- Zoom in, zoom out and fit-map controls stayed fully visible in the upper-right.
- Zoom controls changed the Leaflet image transform.
- The place-selection panel opened successfully.
- Starting-place and destination selectors were visible and usable.
- Portrait bottom sheet and short-landscape side sheet remained inside the viewport.
- No browser console or JavaScript errors were produced by the tested interactions.
- The exact `template-map.svg` asset was loaded.

Laravel CLI route/view rendering could not be fully exercised in the validation container because its PHP build does not include the DOM extension used by Termwind. PHP and JavaScript syntax checks passed.
