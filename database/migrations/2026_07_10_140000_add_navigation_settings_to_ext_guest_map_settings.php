<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ext_guest_map_settings') && ! Schema::hasColumn('ext_guest_map_settings', 'map_north_rotation_deg')) {
            Schema::table('ext_guest_map_settings', function (Blueprint $table): void {
                $table->decimal('map_north_rotation_deg', 7, 3)->default(0)->after('meters_per_pixel');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ext_guest_map_settings') && Schema::hasColumn('ext_guest_map_settings', 'map_north_rotation_deg')) {
            Schema::table('ext_guest_map_settings', function (Blueprint $table): void {
                $table->dropColumn('map_north_rotation_deg');
            });
        }
    }
};
