<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_links', function (Blueprint $table): void {
            if (! Schema::hasColumn('social_links', 'icon_image_path')) {
                $table->string('icon_image_path', 500)->nullable()->after('url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_links', function (Blueprint $table): void {
            if (Schema::hasColumn('social_links', 'icon_image_path')) {
                $table->dropColumn('icon_image_path');
            }
        });
    }
};
