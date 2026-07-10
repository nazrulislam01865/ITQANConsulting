# ITQAN Guest Map Google-Style Update

## What changed

- The starting point is no longer locked to Kids Zone.
- Guests can choose any mapped place from the **Starting point** selector.
- Guests can open a place popup and press **Start here**.
- Guests can press **Choose start on map**, then tap a place marker or anywhere on the map; the nearest mapped place becomes the origin.
- The selected origin is used by route planning, live motion navigation, the graph walker, route links, and the swap control.
- The interface now uses a map-first, Google Maps-style interaction pattern: floating search, route origin/destination card, blue directions controls, circular map controls, route preview card, and mobile bottom sheet.

## Mobile behavior

The public map remains available at:

```text
/external-guest-map
```

For phone motion and orientation tracking, deploy through HTTPS and point the cloud web root to Laravel's `public/` directory.
