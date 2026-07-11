<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string,string> */
    private array $defaultSettings = [
        'save_button_text' => 'Save Contact',
        'qr_alt' => 'Scan to save ITQAN Consulting contact details',
        'qr_caption' => 'Scan to save the digital contact card.',
        'contact_file_name' => 'md-aminul-islam-itqan-consulting.vcf',
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
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')->where('section_key', 'contact_cta')->first();

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
            $updates['label'] = 'Keep our details';
        }

        if (blank($section->title ?? null) || ($section->title ?? null) === 'Send the messy version first.') {
            $updates['title'] = 'Prefer to connect later?';
        }

        if (blank($section->lead ?? null) || ($section->lead ?? null) === 'We can organize it together.') {
            $updates['lead'] = 'Scan the QR code and save the ITQAN digital contact card. Our details will stay on your phone whenever you are ready to start a conversation.';
        }

        if (blank($section->button_text ?? null) || ($section->button_text ?? null) === 'hello@itqanconsulting.com') {
            $updates['button_text'] = 'Start a Conversation';
        }

        if (blank($section->button_url ?? null) || ($section->button_url ?? null) === 'mailto:hello@itqanconsulting.com') {
            $updates['button_url'] = 'https://wa.me/8801742110660';
        }

        if (($section->button_route ?? null) === 'contact') {
            $updates['button_route'] = null;
        }

        DB::table('page_sections')->where('id', $section->id)->update($updates);
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')->where('section_key', 'contact_cta')->first();

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
            'admin_title' => 'CTA Section',
            'settings' => $settings === [] ? null : json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];

        if (($section->label ?? null) === 'Keep our details') {
            $updates['label'] = null;
        }

        if (($section->title ?? null) === 'Prefer to connect later?') {
            $updates['title'] = 'Send the messy version first.';
        }

        if (($section->lead ?? null) === 'Scan the QR code and save the ITQAN digital contact card. Our details will stay on your phone whenever you are ready to start a conversation.') {
            $updates['lead'] = 'We can organize it together.';
        }

        if (($section->button_text ?? null) === 'Start a Conversation') {
            $updates['button_text'] = 'hello@itqanconsulting.com';
        }

        if (($section->button_url ?? null) === 'https://wa.me/8801742110660') {
            $updates['button_url'] = 'mailto:hello@itqanconsulting.com';
        }

        DB::table('page_sections')->where('id', $section->id)->update($updates);
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
