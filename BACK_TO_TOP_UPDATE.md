# Floating Back-to-Top Button Update

Implemented a global frontend floating up-arrow button that smoothly scrolls the page back to the top.

## Files changed

1. `resources/views/frontend/layouts/app.blade.php`
   - Added the global button after the frontend footer.
   - Updated the CSS cache query string to `?v=back-to-top-1`.

2. `public/assets/css/itqan-template.css`
   - Added responsive fixed-position styling.
   - Added a simple fade/slide transition.
   - No flashy animation.

3. `resources/css/app.css`
   - Added the same source CSS for consistency.

4. `resources/js/app.js`
   - Added scroll visibility logic.
   - Added smooth scroll-to-top behavior.
   - Respects the existing `Motion Off` toggle and reduced-motion users.

5. `public/build/assets/app-Dwnwe1VU.js`
   - Patched the already-built JS asset so the change works immediately without running `npm run build`.

## Deployment note

After uploading these files, run:

```bash
php artisan optimize:clear
```

If you rebuild frontend assets later, run:

```bash
npm run build
php artisan optimize:clear
```
