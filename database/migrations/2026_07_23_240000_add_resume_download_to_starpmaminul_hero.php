<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::connection('starpmaminul')->hasTable('portfolio_sections')) {
            return;
        }

        $row = DB::connection('starpmaminul')
            ->table('portfolio_sections')
            ->where('section_key', 'hero')
            ->first();

        if (! $row) {
            return;
        }

        $data = json_decode((string) $row->data, true);

        if (! is_array($data)) {
            $data = [];
        }

        if (
            empty($data['secondary_button'])
            || in_array($data['secondary_button'], ['Print / Save as PDF', 'Print CV', 'Print resume'], true)
        ) {
            $data['secondary_button'] = 'Download resume';
        }

        $data['resume_file'] ??= null;

        DB::connection('starpmaminul')
            ->table('portfolio_sections')
            ->where('section_key', 'hero')
            ->update([
                'data' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::connection('starpmaminul')->hasTable('portfolio_sections')) {
            return;
        }

        $row = DB::connection('starpmaminul')
            ->table('portfolio_sections')
            ->where('section_key', 'hero')
            ->first();

        if (! $row) {
            return;
        }

        $data = json_decode((string) $row->data, true);

        if (! is_array($data)) {
            return;
        }

        if (($data['secondary_button'] ?? null) === 'Download resume') {
            $data['secondary_button'] = 'Print / Save as PDF';
        }

        DB::connection('starpmaminul')
            ->table('portfolio_sections')
            ->where('section_key', 'hero')
            ->update([
                'data' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }
};
