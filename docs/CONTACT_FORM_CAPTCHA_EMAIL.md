# Contact form CAPTCHA and email notification

The public `/contact` form now keeps the existing database/admin workflow and adds two protections/features:

1. Google reCAPTCHA v2 verification before a submission can be stored.
2. An email notification for every valid inquiry. The default recipient is `aminul@itqanconsulting.com`.

## Production environment variables

Add these values to the production `.env` file:

```env
CONTACT_INQUIRY_RECIPIENT=aminul@itqanconsulting.com

CONTACT_CAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_recaptcha_v2_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_v2_secret_key
# Optional extra hostname check, without https:// or a path.
# Set this only when you know the exact production hostname returned by reCAPTCHA:
# RECAPTCHA_EXPECTED_HOSTNAME=www.example.com
```

The site and secret keys must be created as **reCAPTCHA v2 Checkbox** keys for the production domain.

The email uses the Laravel mail transport already configured by the application. Production must therefore also have a working `MAIL_MAILER` / SMTP or API mail configuration. The visitor's email is used as `Reply-To`; the configured `MAIL_FROM_ADDRESS` remains the sender.

After changing `.env` on the server, run:

```bash
php artisan optimize:clear
php artisan config:cache
```

If frontend assets are deployed from source, also run:

```bash
npm install
npm run build
```

## Failure behavior

- Invalid or missing CAPTCHA: the form is not saved and the visitor is returned to the final form step with an error.
- Valid CAPTCHA: the inquiry is saved in `contact_submissions`, so it remains visible in Admin > Contact Responses.
- Email delivery failure: the saved inquiry is kept, the visitor still receives the normal success message, and the mail failure is written to the Laravel log. This avoids data loss and duplicate submissions during temporary mail outages.
