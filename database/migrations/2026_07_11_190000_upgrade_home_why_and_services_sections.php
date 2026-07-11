<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        $this->upgradeWhySection();
        $this->upgradeServicesSection();
    }

    public function down(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        DB::table('home_sections')
            ->where('section_key', 'home_who')
            ->where('admin_title', 'Why ITQAN Exists Section')
            ->update([
                'admin_title' => 'Who We Are Section',
                'updated_at' => now(),
            ]);
    }

    private function upgradeWhySection(): void
    {
        $section = DB::table('home_sections')->where('section_key', 'home_who')->first();

        if (! $section) {
            return;
        }

        $usesOriginalDefaults = in_array($section->label ?? null, ['Who We Are', 'Why ITQAN Exists'], true)
            && in_array($section->title ?? null, [
                'A consulting and technology team for real business problems.',
                'Hard work should not stay trapped inside a messy system.',
            ], true);

        DB::table('home_sections')->where('id', $section->id)->update([
            'admin_title' => 'Why ITQAN Exists Section',
            'label' => $usesOriginalDefaults ? 'Why ITQAN Exists' : $section->label,
            'title' => $usesOriginalDefaults ? 'Hard work should not stay trapped inside a messy system.' : $section->title,
            'lead' => $usesOriginalDefaults
                ? 'As each problem enters view, the visual changes from scattered information into an organized workflow.'
                : $section->lead,
            'updated_at' => now(),
        ]);

        if (! Schema::hasTable('home_section_items')) {
            return;
        }

        $items = [
            [
                'title' => 'The work is happening, but nobody sees the full picture.',
                'text' => 'Information moves through calls, messages, files, and individual knowledge. Management sees the result late.',
                'response' => 'We map the process and make the complete workflow visible.',
                'stage_label' => 'The full picture becomes visible',
            ],
            [
                'title' => 'The team depends on memory, calls, and scattered files.',
                'text' => 'Good people keep the operation running, but repeated follow-up makes growth difficult.',
                'response' => 'We organize the work into practical records, ownership, and systems.',
                'stage_label' => 'Work becomes a shared system',
            ],
            [
                'title' => 'Software is built, but people do not use it properly.',
                'text' => 'The system may be technically correct, yet the user journey does not match the real business routine.',
                'response' => 'We focus on adoption, training, usability, and real business fit.',
                'stage_label' => 'Software fits the people using it',
            ],
            [
                'title' => 'Management does not get the right reports at the right time.',
                'text' => 'Data exists, but it does not answer the questions that leaders need to act on.',
                'response' => 'We design reporting around practical decisions, not decorative dashboards.',
                'stage_label' => 'Decisions get useful information',
            ],
        ];

        if ($usesOriginalDefaults) {
            DB::table('home_section_items')
                ->where('home_section_id', $section->id)
                ->where('item_type', 'card')
                ->delete();

            $now = now();
            foreach ($items as $index => $item) {
                DB::table('home_section_items')->insert([
                    'home_section_id' => $section->id,
                    'item_type' => 'card',
                    'title' => $item['title'],
                    'text' => $item['text'],
                    'settings' => json_encode([
                        'response' => $item['response'],
                        'stage_label' => $item['stage_label'],
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return;
        }

        $existingItems = DB::table('home_section_items')
            ->where('home_section_id', $section->id)
            ->where('item_type', 'card')
            ->orderBy('sort_order')
            ->get();

        foreach ($existingItems as $index => $item) {
            $settings = $this->decodeSettings($item->settings ?? null);
            $fallback = $items[$index] ?? null;
            $settings['response'] = $settings['response'] ?? ($fallback['response'] ?? 'We help organize the work into a clearer and more practical system.');
            $settings['stage_label'] = $settings['stage_label'] ?? ($fallback['stage_label'] ?? ($item->title ?: 'A clearer way of working'));

            DB::table('home_section_items')->where('id', $item->id)->update([
                'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    private function upgradeServicesSection(): void
    {
        $section = DB::table('home_sections')->where('section_key', 'home_services_preview')->first();

        if (! $section) {
            return;
        }

        $usesOriginalDefaults = in_array($section->label ?? null, ['What We Do', 'Services Explorer'], true)
            && in_array($section->title ?? null, [
                'Consulting, software, and delivery support under one roof.',
                'Support from problem understanding to practical execution.',
            ], true);

        DB::table('home_sections')->where('id', $section->id)->update([
            'label' => $usesOriginalDefaults ? 'Services Explorer' : $section->label,
            'title' => $usesOriginalDefaults ? 'Support from problem understanding to practical execution.' : $section->title,
            'lead' => $usesOriginalDefaults
                ? 'Explore the services that can help clarify the work, improve delivery, and build the right solution.'
                : $section->lead,
            'updated_at' => now(),
        ]);

        if (! Schema::hasTable('home_section_items')) {
            return;
        }

        $defaults = [
            [
                'title' => 'Consulting & Business Clarity',
                'text' => 'For businesses where the work is real, but the process, responsibility, scope, or system direction is not clear enough.',
                'common_problem' => 'Scattered process, unclear ownership, and repeated follow-up.',
                'deliverables' => 'Process maps, requirements, scope, action plan, and decision notes.',
            ],
            [
                'title' => 'Project & Product Support',
                'text' => 'For owners and teams that need stronger planning, prioritization, coordination, progress control, and delivery guidance.',
                'common_problem' => 'Late delivery, shifting scope, hidden dependencies, and weak reporting.',
                'deliverables' => 'Roadmap, backlog, project plan, audit, status system, and delivery support.',
            ],
            [
                'title' => 'Software & Web Development',
                'text' => 'For businesses that need custom websites, web applications, dashboards, portals, or business systems designed around real users.',
                'common_problem' => 'Generic tools do not match the business or customer journey.',
                'deliverables' => 'UX direction, system design, development, testing, and launch support.',
            ],
            [
                'title' => 'ERP & Automation',
                'text' => 'For growing companies that need connected records, structured approvals, better reporting, and less manual repetition.',
                'common_problem' => 'Departments work separately and information does not flow well.',
                'deliverables' => 'ERP scope, module mapping, automation design, and implementation support.',
            ],
            [
                'title' => 'Training & Coaching',
                'text' => 'For teams and professionals who need practical learning in project management, business analysis, tools, AI, and delivery discipline.',
                'common_problem' => 'Knowledge exists, but day-to-day application remains weak.',
                'deliverables' => 'Workshops, coaching, templates, exercises, and team learning plans.',
            ],
            [
                'title' => 'Dedicated Team Support',
                'text' => 'For organizations that need reliable project, product, design, development, QA, or operational capacity for ongoing work.',
                'common_problem' => 'Hiring takes time and existing teams cannot carry every priority.',
                'deliverables' => 'Role planning, team setup, managed resources, and delivery reporting.',
            ],
        ];

        $items = DB::table('home_section_items')
            ->where('home_section_id', $section->id)
            ->where('item_type', 'service_card')
            ->orderBy('sort_order')
            ->get();

        foreach ($items as $index => $item) {
            $default = $defaults[$index] ?? null;
            $settings = $this->decodeSettings($item->settings ?? null);
            $settings['common_problem'] = $settings['common_problem'] ?? ($default['common_problem'] ?? 'The work lacks a clear structure, owner, or practical next step.');
            $settings['deliverables'] = $settings['deliverables'] ?? ($default['deliverables'] ?? 'A clear scope, practical recommendations, and an actionable delivery plan.');

            DB::table('home_section_items')->where('id', $item->id)->update([
                'title' => $usesOriginalDefaults && $default ? $default['title'] : $item->title,
                'text' => $usesOriginalDefaults && $default ? $default['text'] : $item->text,
                'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
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
