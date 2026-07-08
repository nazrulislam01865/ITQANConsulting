# Work Image Link Update

Updated the frontend work/case-study cards so the image/visual area is clickable and opens the same URL as the `View Case Study` button.

## Changed files

- `resources/views/frontend/partials/work-card.blade.php`
  - Wrapped the `.work-visual` area with an anchor tag.
  - Uses the same `$buttonHref` already used by the case-study button.

- `public/assets/css/itqan-template.css`
  - Added clean link styling for `.work-visual-link`.
  - Added keyboard focus outline for accessibility.

- `resources/css/app.css`
  - Kept source CSS in sync with the public template CSS.

- `resources/views/frontend/layouts/app.blade.php`
  - Bumped the CSS query string to avoid old browser cache.

## Deploy note

After uploading the files, run:

```bash
php artisan optimize:clear
```
