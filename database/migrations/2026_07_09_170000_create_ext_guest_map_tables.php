<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_guest_map_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('The Palace Resort Map');
            $table->string('map_type')->default('template_svg');
            $table->string('map_file')->nullable();
            $table->unsignedInteger('width')->default(1200);
            $table->unsignedInteger('height')->default(800);
            $table->decimal('meters_per_pixel', 8, 4)->default(1.45);
            $table->unsignedInteger('walk_meters_per_minute')->default(75);
            $table->unsignedInteger('buggy_meters_per_minute')->default(180);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ext_guest_map_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->default('#1f6b4b');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ext_guest_map_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_setting_id')->constrained('ext_guest_map_settings')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('x', 10, 3);
            $table->decimal('y', 10, 3);
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('node_type')->default('junction');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['map_setting_id', 'code'], 'ext_gm_nodes_setting_code_unique');
        });

        Schema::create('ext_guest_map_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_setting_id')->constrained('ext_guest_map_settings')->cascadeOnDelete();
            $table->foreignId('map_category_id')->nullable()->constrained('ext_guest_map_categories')->nullOnDelete();
            $table->foreignId('map_node_id')->nullable()->constrained('ext_guest_map_nodes')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('pin_number')->nullable();
            $table->decimal('x', 10, 3);
            $table->decimal('y', 10, 3);
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_qr_point')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['map_setting_id', 'slug'], 'ext_gm_places_setting_slug_unique');
        });

        Schema::create('ext_guest_map_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_setting_id')->constrained('ext_guest_map_settings')->cascadeOnDelete();
            $table->foreignId('from_node_id')->constrained('ext_guest_map_nodes')->cascadeOnDelete();
            $table->foreignId('to_node_id')->constrained('ext_guest_map_nodes')->cascadeOnDelete();
            $table->json('path_points')->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->boolean('walk_enabled')->default(true);
            $table->boolean('buggy_enabled')->default(true);
            $table->boolean('staff_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('ext_guest_map_qr_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_place_id')->constrained('ext_guest_map_places')->cascadeOnDelete();
            $table->string('title');
            $table->string('qr_code')->unique();
            $table->string('qr_url');
            $table->string('printed_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ext_guest_map_route_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_uuid')->nullable()->index();
            $table->foreignId('from_place_id')->nullable()->constrained('ext_guest_map_places')->nullOnDelete();
            $table->foreignId('to_place_id')->nullable()->constrained('ext_guest_map_places')->nullOnDelete();
            $table->string('from_label')->nullable();
            $table->string('to_label')->nullable();
            $table->decimal('start_x', 10, 3)->nullable();
            $table->decimal('start_y', 10, 3)->nullable();
            $table->decimal('current_x', 10, 3)->nullable();
            $table->decimal('current_y', 10, 3)->nullable();
            $table->decimal('start_lat', 12, 8)->nullable();
            $table->decimal('start_lng', 12, 8)->nullable();
            $table->decimal('current_lat', 12, 8)->nullable();
            $table->decimal('current_lng', 12, 8)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->decimal('gps_distance_meters', 10, 2)->default(0);
            $table->unsignedInteger('walk_minutes')->nullable();
            $table->unsignedInteger('buggy_minutes')->nullable();
            $table->json('route_path')->nullable();
            $table->json('node_path')->nullable();
            $table->json('steps')->nullable();
            $table->string('mode')->default('walk');
            $table->string('status')->default('planned')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_tracked_at')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('ext_guest_map_location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_route_log_id')->nullable()->constrained('ext_guest_map_route_logs')->nullOnDelete();
            $table->uuid('session_uuid')->nullable()->index();
            $table->decimal('lat', 12, 8)->nullable();
            $table->decimal('lng', 12, 8)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->decimal('heading', 8, 2)->nullable();
            $table->decimal('speed_meters_per_second', 8, 3)->nullable();
            $table->decimal('map_x', 10, 3)->nullable();
            $table->decimal('map_y', 10, 3)->nullable();
            $table->decimal('gps_distance_meters', 10, 2)->default(0);
            $table->decimal('route_progress_percent', 6, 2)->default(0);
            $table->string('source')->default('browser_geolocation');
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_guest_map_location_logs');
        Schema::dropIfExists('ext_guest_map_route_logs');
        Schema::dropIfExists('ext_guest_map_qr_points');
        Schema::dropIfExists('ext_guest_map_edges');
        Schema::dropIfExists('ext_guest_map_places');
        Schema::dropIfExists('ext_guest_map_nodes');
        Schema::dropIfExists('ext_guest_map_categories');
        Schema::dropIfExists('ext_guest_map_settings');
    }
};
