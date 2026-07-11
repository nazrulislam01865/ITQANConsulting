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

        DB::table('home_sections')
            ->where('section_key', 'home_founder')
            ->update([
                'admin_title' => "Founder's Message Section",
                'updated_at' => now(),
            ]);

        $section = DB::table('home_sections')->where('section_key', 'home_problems')->first();

        if (! $section) {
            return;
        }

        $usesOriginalDefaults = ($section->label ?? null) === 'Why ITQAN Exists'
            && ($section->title ?? null) === 'Because many businesses are working hard, but still feel stuck.';

        DB::table('home_sections')->where('id', $section->id)->update([
            'admin_title' => 'Interactive Clarity Check Section',
            'label' => $usesOriginalDefaults ? 'Interactive Clarity Check' : $section->label,
            'title' => $usesOriginalDefaults ? 'Where does the work feel unclear?' : $section->title,
            'lead' => $usesOriginalDefaults
                ? 'Choose the situation that sounds familiar. The response changes instantly and shows a practical starting point.'
                : $section->lead,
            'updated_at' => now(),
        ]);

        if (! Schema::hasTable('home_section_items')) {
            return;
        }

        if ($usesOriginalDefaults) {
            DB::table('home_section_items')
                ->where('home_section_id', $section->id)
                ->where('item_type', 'problem')
                ->delete();

            $now = now();
            $items = [
                ['Our process is undocumented', 'Important work depends on people remembering what to do, when to do it, and who should approve it.', ['Process interviews', 'Workflow mapping', 'Roles and responsibility', 'Practical documentation']],
                ['Projects keep getting delayed', 'The team is working, but scope, priority, dependencies, and decisions are difficult to control.', ['Project audit', 'Scope review', 'Delivery planning', 'Progress reporting']],
                ['Reports take too much time', 'Management needs answers, but data is scattered and reporting requires repeated manual preparation.', ['Data mapping', 'Dashboard planning', 'Report structure', 'Automation opportunities']],
                ['Too much work is manual', 'People repeat the same entries, approvals, reminders, and follow-ups across calls, files, and messages.', ['Manual task review', 'Automation map', 'Priority matrix', 'Implementation plan']],
                ['Software does not match the business', 'A system exists, but the real work happens somewhere else because users cannot follow the intended flow.', ['User interviews', 'Workflow-fit review', 'Adoption planning', 'Improvement backlog']],
                ['We are unsure what system we need', 'The business wants technology, but the right scope, budget, priority, and implementation path are not clear yet.', ['Discovery workshop', 'Solution options', 'Scope definition', 'Delivery roadmap']],
            ];

            foreach ($items as $index => [$problem, $summary, $services]) {
                DB::table('home_section_items')->insert([
                    'home_section_id' => $section->id,
                    'item_type' => 'problem',
                    'title' => $problem,
                    'text' => $summary,
                    'settings' => json_encode([
                        'problem' => $problem,
                        'summary' => $summary,
                        'services' => implode("\n", $services),
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
            ->where('item_type', 'problem')
            ->get();

        foreach ($existingItems as $item) {
            $settings = $this->decodeSettings($item->settings ?? null);
            $summary = trim((string) ($settings['summary'] ?? $settings['response'] ?? $item->text ?? ''));

            if (! array_key_exists('summary', $settings)) {
                $settings['summary'] = $summary;
            }

            if (! array_key_exists('services', $settings) && filled($settings['response'] ?? null)) {
                $settings['services'] = (string) $settings['response'];
            }

            DB::table('home_section_items')->where('id', $item->id)->update([
                'title' => $item->title ?: ($settings['problem'] ?? null),
                'text' => $item->text ?: $summary,
                'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        DB::table('home_sections')
            ->where('section_key', 'home_founder')
            ->where('admin_title', "Founder's Message Section")
            ->update([
                'admin_title' => 'Founder Section',
                'updated_at' => now(),
            ]);

        DB::table('home_sections')
            ->where('section_key', 'home_problems')
            ->where('admin_title', 'Interactive Clarity Check Section')
            ->update([
                'admin_title' => 'Why ITQAN Exists Section',
                'updated_at' => now(),
            ]);
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
