<?php

namespace App\Services\Frontend;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecaptchaVerifier
{
    public function isEnabled(): bool
    {
        return (bool) config('itqan_contact.captcha.enabled', false)
            && trim((string) config('itqan_contact.captcha.site_key')) !== ''
            && trim((string) config('itqan_contact.captcha.secret_key')) !== '';
    }

    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        $token = trim((string) $token);
        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout((int) config('itqan_contact.captcha.timeout_seconds', 5))
                ->post((string) config('itqan_contact.captcha.verify_url'), array_filter([
                    'secret' => (string) config('itqan_contact.captcha.secret_key'),
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ], static fn ($value): bool => $value !== null && $value !== ''));

            if (! $response->successful() || ! (bool) $response->json('success', false)) {
                Log::notice('Contact form reCAPTCHA verification was rejected.', [
                    'status' => $response->status(),
                    'error_codes' => $response->json('error-codes', []),
                ]);

                return false;
            }

            $expectedHostname = trim((string) config('itqan_contact.captcha.expected_hostname'));
            if ($expectedHostname !== '') {
                $actualHostname = strtolower(trim((string) $response->json('hostname', '')));
                if ($actualHostname !== strtolower($expectedHostname)) {
                    Log::warning('Contact form reCAPTCHA hostname did not match.', [
                        'expected' => $expectedHostname,
                        'actual' => $actualHostname,
                    ]);

                    return false;
                }
            }

            return true;
        } catch (Throwable $exception) {
            Log::warning('Contact form reCAPTCHA verification could not be completed.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
