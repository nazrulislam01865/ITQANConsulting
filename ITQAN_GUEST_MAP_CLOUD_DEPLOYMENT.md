# ITQAN Guest Map — Cloud Deployment

## Required server setup

- PHP 8.3 or newer, matching `composer.json`
- A supported PDO driver for the selected database, such as `pdo_mysql` or `pdo_sqlite`
- Standard Laravel extensions including OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo, and BCMath
- HTTPS on the public domain
- Web root set to the project's `public/` directory
- Writable `storage/` and `bootstrap/cache/` directories

## Environment

Set at least:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_SECURE_COOKIE=true
```

Generate the application key once when creating a new environment:

```bash
php artisan key:generate --force
```

## Deploy commands

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=ItqanExternalGuestMapSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

The guest map scripts and styles are already committed under `public/assets/itqan-external-guest-map/`; no Vite build is required specifically for the map update. The rest of the ITQAN site can continue using the existing `public/build` output.

## Mobile Chrome verification

1. Open `https://your-domain.example/external-guest-map` in Chrome on the phone.
2. Choose a destination and tap **Start live navigation** directly from a user gesture.
3. Allow Motion & Orientation access if the browser asks.
4. Hold the phone facing the walking direction and move it normally.
5. Confirm the page response includes the `Permissions-Policy` header for accelerometer, gyroscope, magnetometer, geolocation, and wake lock.

An HTTP URL, an invalid certificate, or an HTTPS page loading scripts over HTTP will prevent mobile sensor access.
