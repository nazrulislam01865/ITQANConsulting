<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string,string> */
    private array $defaultSettings = [
        'save_button_text' => 'Save Contact',
        'qr_alt' => 'ITQAN Consulting digital contact QR code',
        'qr_caption' => 'Scan to save the digital contact card directly to your phone.',
        'contact_file_name' => 'ITQAN-Consulting-Digital-Card.vcf',
        'first_name' => 'Md Aminul',
        'last_name' => 'Islam',
        'full_name' => 'Md Aminul Islam, PMP, CSM',
        'credentials' => 'PMP, CSM',
        'organization' => 'ITQAN Consulting',
        'job_title' => 'Founder & Chief Consultant',
        'phone' => '+8801742110660',
        'whatsapp' => '+8801742110660',
        'email' => 'aminul@itqanconsulting.com',
        'website' => 'https://itqanconsulting.com',
        'note' => 'ITQAN Consulting provides business consulting, project and product management, software and web development, ERP and automation, UI/UX design, training, coaching, and dedicated team support. Remote team in Bangladesh. Serving international clients.',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        $section = DB::table('home_sections')->where('section_key', 'home_cta')->first();

        if (! $section) {
            return;
        }

        $settings = $this->decodeSettings($section->settings ?? null);
        $settings = array_merge($this->defaultSettings, $settings);

        $updates = [
            'admin_title' => 'Digital Contact Card Section',
            'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];

        if (blank($section->label ?? null)) {
            $updates['label'] = 'Start with a conversation';
        }

        if (blank($section->lead ?? null) || $section->lead === 'Let’s talk first. No pressure. No hard selling. We will understand your situation and suggest a practical next step.') {
            $updates['lead'] = 'Let’s talk first. No pressure. No hard selling. We will understand your situation, help organize the problem, and suggest a practical next step.';
        }

        if (blank($section->button_text ?? null) || in_array($section->button_text, ['Contact', 'Contact ITQAN'], true)) {
            $updates['button_text'] = 'Start a Conversation';
        }

        if (blank($section->button_url ?? null)) {
            $updates['button_url'] = 'https://wa.me/8801742110660';
        }

        if (($section->button_route ?? null) === 'contact') {
            $updates['button_route'] = null;
        }

        DB::table('home_sections')->where('id', $section->id)->update($updates);
    }

    public function down(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        $section = DB::table('home_sections')->where('section_key', 'home_cta')->first();

        if (! $section) {
            return;
        }

        $settings = $this->decodeSettings($section->settings ?? null);

        foreach ($this->defaultSettings as $key => $value) {
            if (($settings[$key] ?? null) === $value) {
                unset($settings[$key]);
            }
        }

        $updates = [
            'admin_title' => 'Final CTA Section',
            'settings' => $settings === [] ? null : json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];

        if (($section->label ?? null) === 'Start with a conversation') {
            $updates['label'] = null;
        }

        if (($section->lead ?? null) === 'Let’s talk first. No pressure. No hard selling. We will understand your situation, help organize the problem, and suggest a practical next step.') {
            $updates['lead'] = 'Let’s talk first. No pressure. No hard selling. We will understand your situation and suggest a practical next step.';
        }

        if (($section->button_text ?? null) === 'Start a Conversation') {
            $updates['button_text'] = 'Contact ITQAN';
        }

        if (($section->button_url ?? null) === 'https://wa.me/8801742110660') {
            $updates['button_url'] = null;
            $updates['button_route'] = 'contact';
        }

        DB::table('home_sections')->where('id', $section->id)->update($updates);
    }

    /** @return array<string,mixed> */
    private function decodeSettings(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
};
