# Social Icon Image/Favicon Update

## What changed

The Social Links admin no longer asks the admin to paste raw SVG path/code.

Each social link now supports:

- Platform text
- Label
- URL
- Optional uploaded icon image
- Active/inactive status

If no icon image is uploaded, the frontend automatically generates an icon from the social link domain/favicon. If no useful URL is present, it falls back to a safe platform icon.

## Database

A new nullable column was added:

```txt
social_links.icon_image_path
```

Existing `icon_svg` is kept for backward compatibility but is no longer shown in admin or used as the main editing method.

## Upload rules

Allowed icon uploads:

```txt
jpg, jpeg, png, webp
max 512 KB
```

## Why this is better

- Admin users do not need to paste SVG code.
- The same social links are reused across Home, Contact, Footer, and future sections.
- Icons can be replaced visually with a normal upload field.
- Fallback icons keep the frontend from breaking when an upload is missing.
