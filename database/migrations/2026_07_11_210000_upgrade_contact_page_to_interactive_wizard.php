<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $form = DB::table('page_sections')->where('section_key', 'contact_form')->first();

        if ($form) {
            $settings = $this->decodeSettings($form->settings ?? null);

            $oldNeeds = implode("\n", [
                'Not sure yet',
                'Business process',
                'Project management',
                'Software development',
                'ERP',
                'Website',
                'Automation',
                'Training',
                'Dedicated team',
            ]);
            $oldMethods = implode("\n", ['WhatsApp', 'Email', 'Phone call', 'Google Meet']);

            $defaults = [
                'problems' => implode("\n", [
                    'Business process',
                    'Project delivery',
                    'Software or website',
                    'ERP or automation',
                    'Reporting and data',
                    'Team capability',
                ]),
                'needs' => implode("\n", [
                    'Consulting first',
                    'Plan and manage',
                    'Design and build',
                    'Review current system',
                    'Train the team',
                    'Not sure yet',
                ]),
                'methods' => implode("\n", ['WhatsApp', 'Email', 'Phone call', 'Online meeting']),
                'budgets' => implode("\n", [
                    'Not decided yet',
                    'Under BDT 100,000',
                    'BDT 100,000 – 300,000',
                    'BDT 300,000 – 800,000',
                    'Above BDT 800,000',
                ]),
                'problem_step_title' => 'What feels unclear right now?',
                'problem_step_text' => 'Select one or more. It does not need to be perfectly described.',
                'support_step_title' => 'What kind of support may help?',
                'support_step_text' => 'Choose what seems closest. ITQAN can help refine the scope later.',
                'details_step_title' => 'Share the basic details.',
                'details_step_text' => 'This helps us respond in a useful way.',
                'message_step_title' => 'Describe the situation in your own words.',
                'message_step_text' => 'It is okay if the information is incomplete.',
                'submit_text' => 'Send the messy version',
                'success_title' => 'Thank you. The first step is clear.',
                'success_text' => 'Your message has been received. ITQAN will contact you soon.',
            ];

            if (! isset($settings['needs']) || trim((string) $settings['needs']) === trim($oldNeeds)) {
                $settings['needs'] = $defaults['needs'];
            }
            if (! isset($settings['methods']) || trim((string) $settings['methods']) === trim($oldMethods)) {
                $settings['methods'] = $defaults['methods'];
            }

            foreach ($defaults as $key => $value) {
                if (! array_key_exists($key, $settings) || trim((string) $settings[$key]) === '') {
                    $settings[$key] = $value;
                }
            }

            $label = $this->replaceKnownDefault(
                $form->label ?? null,
                ['What happens next?', ''],
                'Start a conversation'
            );
            $title = $this->replaceKnownDefault(
                $form->title ?? null,
                ['No hard selling. No pressure.', ''],
                'Send the messy version. We will help organize it.'
            );
            $lead = $this->replaceKnownDefault(
                $form->lead ?? null,
                ['We read your message and ask a few useful questions. Then we suggest a practical next step.', ''],
                'No pressure. No hard selling. The form reveals one decision at a time so the first contact feels easier.'
            );

            DB::table('page_sections')->where('id', $form->id)->update([
                'admin_title' => 'Interactive Contact Form',
                'label' => $label,
                'title' => $title,
                'lead' => $lead,
                'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }

        $qrCard = DB::table('page_sections')->where('section_key', 'contact_cta')->first();

        if ($qrCard) {
            DB::table('page_sections')->where('id', $qrCard->id)->update([
                'admin_title' => 'QR Contact Card',
                'label' => $this->replaceKnownDefault(
                    $qrCard->label ?? null,
                    ['Keep our details', ''],
                    'Save the contact'
                ),
                'title' => $this->replaceKnownDefault(
                    $qrCard->title ?? null,
                    ['Prefer to connect later?', ''],
                    'Keep ITQAN one scan away.'
                ),
                'lead' => $this->replaceKnownDefault(
                    $qrCard->lead ?? null,
                    ['Scan the QR code and save the ITQAN digital contact card. Our details will stay on your phone whenever you are ready to start a conversation.', ''],
                    'Scan the QR code to save the founder’s contact details directly to your phone.'
                ),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Content migration: existing customized values and uploaded QR images are intentionally preserved.
    }

    /** @return array<string,mixed> */
    private function decodeSettings(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<int,string> $knownDefaults */
    private function replaceKnownDefault(?string $current, array $knownDefaults, string $replacement): string
    {
        $normalized = trim((string) $current);

        foreach ($knownDefaults as $knownDefault) {
            if ($normalized === trim($knownDefault)) {
                return $replacement;
            }
        }

        return $normalized;
    }
};
