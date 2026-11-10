# Contact form CAPTCHA and email notification

The public `/contact` form retains the existing database/admin workflow and includes:

1. Google reCAPTCHA v2 verification before a submission can be stored.
2. A well-formatted email notification sent to one configured recipient. The default recipient is `aminul@itqanconsulting.com`.

## Production environment variables

Add these values to the production `.env` file:

```env
CONTACT_INQUIRY_RECIPIENT=aminul@itqanconsulting.com
MAIL_MAILER=e2a
E2A_API_URL=https://api.e2a.dev/v1
E2A_API_KEY=your_e2a_api_key
E2A_FROM_EMAIL=your-registered-agent@agents.e2a.dev
MAIL_FROM_ADDRESS=your-registered-agent@agents.e2a.dev
MAIL_FROM_NAME="ITQAN Consulting"

CONTACT_CAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_recaptcha_v2_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_v2_secret_key
# Optional extra hostname check, without https:// or a path.
# Set this only when you know the exact production hostname returned by reCAPTCHA:
# RECAPTCHA_EXPECTED_HOSTNAME=www.example.com
```

The site and secret keys must be created as **reCAPTCHA v2 Checkbox** keys for the production domain.

Only `CONTACT_INQUIRY_RECIPIENT` receives the contact inquiry notification. No forwarding recipient is configured.

The E2A transport sends through the configured E2A sender identity. The `E2A_FROM_EMAIL` and `MAIL_FROM_ADDRESS` values should be the same registered sender. The visitor's email remains the `Reply-To` address. Leave your existing real API key in `.env`; do not add it to the project ZIP.

After changing `.env` on the server, run:

```bash
php artisan optimize:clear
php artisan config:cache
```

## Failure behavior

- Invalid or missing CAPTCHA (or missing CAPTCHA keys when enabled): the form is not saved and the visitor is returned to the final form step with an error.
- Valid CAPTCHA: the inquiry is saved in `contact_submissions`, so it remains visible in Admin > Contact Responses.
- Email delivery failure: the saved inquiry is retained, the visitor still receives the normal success message, and the mail failure is logged. Check the E2A message status for final delivery because API acceptance does not prove inbox delivery.
