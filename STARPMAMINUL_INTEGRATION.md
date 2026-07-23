# Md Aminul Islam Portfolio Integration

The Md Aminul Islam portfolio is installed as an isolated module inside the ITQAN Consulting Laravel application.

## URLs

- Portfolio: `/starpmaminul`
- Portfolio admin login: `/starpmaminul/admin/login`
- Portfolio admin dashboard: `/starpmaminul/admin`
- Existing ITQAN website: `/`
- Existing ITQAN admin: `/admin/login`

## Separation

The portfolio module has its own:

- Controllers: `app/Http/Controllers/StarPmAminul`
- Request validation: `app/Http/Requests/StarPmAminul`
- Models: `app/Models/StarPmAminul`
- Service layer: `app/Services/StarPmAminul`
- Views: `resources/views/starpmaminul`
- Blade components: `resources/views/components/starpmaminul`
- CSS and JavaScript entry points: `resources/css/starpmaminul` and `resources/js/starpmaminul`
- Authentication guard and user provider: `starpmaminul`
- Upload directory: `storage/app/public/starpmaminul`
- Database connection: `starpmaminul`
- Default local database: `database/starpmaminul.sqlite`

The ITQAN website and its `/admin` authentication remain unchanged.

## Local installation

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

Compiled assets are included. Run these only when editing frontend source files:

```bash
npm install
npm run build
```

## Default portfolio administrator

- Email: `admin@aminulislam.com`
- Password: `Admin@12345`

Change the password before production deployment.

## Separate MySQL database in production

Create a dedicated database, for example `itqan_starpmaminul`, and add the following to `.env`:

```env
STARPMAMINUL_DB_CONNECTION=mysql
STARPMAMINUL_DB_HOST=127.0.0.1
STARPMAMINUL_DB_PORT=3306
STARPMAMINUL_DB_DATABASE=itqan_starpmaminul
STARPMAMINUL_DB_USERNAME=your_portfolio_db_user
STARPMAMINUL_DB_PASSWORD=your_portfolio_db_password

STARPMAMINUL_ADMIN_NAME="Portfolio Admin"
STARPMAMINUL_ADMIN_EMAIL=admin@aminulislam.com
STARPMAMINUL_ADMIN_PASSWORD=replace-with-a-strong-password
```

Then run:

```bash
php artisan migrate --force
php artisan db:seed --class="Database\\Seeders\\StarPmAminulSeeder" --force
php artisan optimize:clear
```

The main ITQAN database variables remain under the normal `DB_*` settings and are not reused by the portfolio models.
