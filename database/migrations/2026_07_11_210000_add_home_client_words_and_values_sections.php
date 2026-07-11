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

        $this->upgradeClientWords();
        $this->createValuesSection();

        DB::table('home_sections')
            ->where('section_key', 'home_cta')
            ->where('sort_order', '<=', 90)
            ->update(['sort_order' => 100, 'updated_at' => now()]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        $valuesId = DB::table('home_sections')->where('section_key', 'home_values')->value('id');

        if ($valuesId && Schema::hasTable('home_section_items')) {
            DB::table('home_section_items')->where('home_section_id', $valuesId)->delete();
        }

        DB::table('home_sections')->where('section_key', 'home_values')->delete();
    }

    private function upgradeClientWords(): void
    {
        $section = DB::table('home_sections')->where('section_key', 'home_testimonials')->first();

        if (! $section) {
            return;
        }

        DB::table('home_sections')->where('id', $section->id)->update([
            'admin_title' => 'Client Words Section',
            'lead' => $section->lead ?: 'A calm slider with enough time to read. Autoplay pauses on hover, focus, and touch.',
            'updated_at' => now(),
        ]);

        if (! Schema::hasTable('home_section_items')) {
            return;
        }

        $projects = ['Business clarity', 'Consulting', 'Project delivery', 'Coaching'];
        $items = DB::table('home_section_items')
            ->where('home_section_id', $section->id)
            ->where('item_type', 'testimonial')
            ->orderBy('sort_order')
            ->get();

        foreach ($items as $index => $item) {
            $settings = $this->decodeSettings($item->settings ?? null);
            $settings['project'] = $settings['project'] ?? ($projects[$index] ?? 'Client work');

            DB::table('home_section_items')->where('id', $item->id)->update([
                'settings' => json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    private function createValuesSection(): void
    {
        $section = DB::table('home_sections')->where('section_key', 'home_values')->first();
        $now = now();

        if (! $section) {
            $sectionId = DB::table('home_sections')->insertGetId([
                'section_key' => 'home_values',
                'admin_title' => 'How We Think Section',
                'label' => 'How We Think',
                'title' => 'Principles that shape the work.',
                'lead' => 'The number follows the principle currently in view.',
                'settings' => json_encode([], JSON_UNESCAPED_SLASHES),
                'sort_order' => 90,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $sectionId = $section->id;
            DB::table('home_sections')->where('id', $sectionId)->update([
                'admin_title' => 'How We Think Section',
                'sort_order' => 90,
                'updated_at' => $now,
            ]);
        }

        if (! Schema::hasTable('home_section_items')) {
            return;
        }

        $hasItems = DB::table('home_section_items')
            ->where('home_section_id', $sectionId)
            ->where('item_type', 'value')
            ->exists();

        if ($hasItems) {
            return;
        }

        $items = [
            [
                'num' => '01',
                'mini' => '01 / clarity before execution',
                'title' => 'We do not start with software. We start with the work.',
                'text' => 'Understanding the real process prevents the team from building the wrong solution faster.',
                'example' => 'Example: A process and reporting fix may solve the problem before a large system is considered.',
            ],
            [
                'num' => '02',
                'mini' => '02 / keep it practical',
                'title' => 'Simple where simple works.',
                'text' => 'Not every problem needs a complex platform. The right answer may be a clearer responsibility, a better form, or one useful dashboard.',
                'example' => 'Example: One well-designed approval flow can remove weeks of repeated follow-up.',
            ],
            [
                'num' => '03',
                'mini' => '03 / fit the business',
                'title' => 'Technology is useful only when people can use it in real work.',
                'text' => 'We consider users, habits, data, controls, adoption, and the way the operation actually runs.',
                'example' => 'Example: A technically correct ERP still fails when the user journey does not match daily operations.',
            ],
            [
                'num' => '04',
                'mini' => '04 / honest advice',
                'title' => 'We say what is needed, and what is not.',
                'text' => 'Clear advice protects the client’s time, budget, and attention. We do not add scope only to make the project bigger.',
                'example' => 'Example: Sometimes the right recommendation is to improve the current system rather than replace it.',
            ],
        ];

        foreach ($items as $index => $item) {
            DB::table('home_section_items')->insert([
                'home_section_id' => $sectionId,
                'item_type' => 'value',
                'badge' => $item['num'],
                'title' => $item['title'],
                'text' => $item['text'],
                'settings' => json_encode([
                    'mini' => $item['mini'],
                    'example' => $item['example'],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'sort_order' => ($index + 1) * 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
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
