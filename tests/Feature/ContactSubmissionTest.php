<?php

namespace Tests\Feature;

use App\Mail\ContactInquiryReceived;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'itqan_contact.recipient_email' => 'aminul@itqanconsulting.com',
            'itqan_contact.captcha.enabled' => false,
        ]);
    }

    public function test_contact_inquiry_is_saved_and_emailed_to_the_configured_recipient(): void
    {
        Mail::fake();

        $response = $this->from(route('contact'))->post(route('contact.submit'), $this->validPayload());

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Test Visitor',
            'email' => 'visitor@example.com',
            'status' => 'unread',
        ]);

        Mail::assertSent(ContactInquiryReceived::class, function (ContactInquiryReceived $mail): bool {
            return $mail->hasTo('aminul@itqanconsulting.com')
                && $mail->submission->email === 'visitor@example.com';
        });
    }

    public function test_contact_inquiry_is_rejected_when_recaptcha_verification_fails(): void
    {
        Mail::fake();
        $this->enableCaptcha();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            ...$this->validPayload(),
            'g-recaptcha-response' => 'invalid-token',
        ]);

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors('captcha');

        $this->assertDatabaseCount('contact_submissions', 0);
        Mail::assertNothingSent();
    }

    public function test_contact_inquiry_is_saved_when_recaptcha_verification_succeeds(): void
    {
        Mail::fake();
        $this->enableCaptcha();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'hostname' => 'itqanconsulting.com',
            ], 200),
        ]);

        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            ...$this->validPayload(),
            'g-recaptcha-response' => 'valid-token',
        ]);

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_submissions', 1);
        Mail::assertSent(ContactInquiryReceived::class, 1);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'name' => 'Test Visitor',
            'company' => 'Example Company',
            'email' => 'visitor@example.com',
            'phone' => '+8801700000000',
            'areas' => ['Business process'],
            'support_types' => ['Consulting first'],
            'budget' => 'Not decided yet',
            'method' => 'Email',
            'message' => 'We need help reviewing our current business process and deciding the next practical step.',
        ];
    }

    private function enableCaptcha(): void
    {
        config([
            'itqan_contact.captcha.enabled' => true,
            'itqan_contact.captcha.site_key' => 'test-site-key',
            'itqan_contact.captcha.secret_key' => 'test-secret-key',
            'itqan_contact.captcha.verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
            'itqan_contact.captcha.expected_hostname' => null,
        ]);
    }
}
