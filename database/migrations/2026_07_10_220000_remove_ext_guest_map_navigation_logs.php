<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ext_guest_map_location_logs');
        Schema::dropIfExists('ext_guest_map_route_logs');
    }

    public function down(): void
    {
        // Navigation logging was intentionally removed from the map-only integration.
    }
};
