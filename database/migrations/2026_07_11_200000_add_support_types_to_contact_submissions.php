<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contact_submissions') || Schema::hasColumn('contact_submissions', 'support_types')) {
            return;
        }

        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->json('support_types')->nullable()->after('areas');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contact_submissions') || ! Schema::hasColumn('contact_submissions', 'support_types')) {
            return;
        }

        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->dropColumn('support_types');
        });
    }
};
