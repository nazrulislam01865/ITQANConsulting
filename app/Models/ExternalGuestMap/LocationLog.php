<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_location_logs';

    protected $fillable = [
        'map_route_log_id', 'session_uuid', 'lat', 'lng', 'accuracy_meters', 'altitude', 'heading',
        'speed_meters_per_second', 'map_x', 'map_y', 'gps_distance_meters', 'route_progress_percent',
        'source', 'user_agent',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'accuracy_meters' => 'float',
        'altitude' => 'float',
        'heading' => 'float',
        'speed_meters_per_second' => 'float',
        'map_x' => 'float',
        'map_y' => 'float',
        'gps_distance_meters' => 'float',
        'route_progress_percent' => 'float',
    ];

    public function routeLog()
    {
        return $this->belongsTo(RouteLog::class, 'map_route_log_id');
    }
}
