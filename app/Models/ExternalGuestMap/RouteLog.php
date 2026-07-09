<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteLog extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_route_logs';

    protected $fillable = [
        'session_uuid', 'from_place_id', 'to_place_id', 'from_label', 'to_label', 'start_x', 'start_y',
        'current_x', 'current_y', 'start_lat', 'start_lng', 'current_lat', 'current_lng', 'accuracy_meters',
        'distance_meters', 'gps_distance_meters', 'walk_minutes', 'buggy_minutes', 'route_path', 'node_path',
        'steps', 'mode', 'status', 'started_at', 'ended_at', 'last_tracked_at', 'user_agent',
    ];

    protected $casts = [
        'start_x' => 'float',
        'start_y' => 'float',
        'current_x' => 'float',
        'current_y' => 'float',
        'start_lat' => 'float',
        'start_lng' => 'float',
        'current_lat' => 'float',
        'current_lng' => 'float',
        'accuracy_meters' => 'float',
        'distance_meters' => 'float',
        'gps_distance_meters' => 'float',
        'walk_minutes' => 'integer',
        'buggy_minutes' => 'integer',
        'route_path' => 'array',
        'node_path' => 'array',
        'steps' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_tracked_at' => 'datetime',
    ];

    public function fromPlace()
    {
        return $this->belongsTo(Place::class, 'from_place_id');
    }

    public function toPlace()
    {
        return $this->belongsTo(Place::class, 'to_place_id');
    }

    public function locationLogs()
    {
        return $this->hasMany(LocationLog::class, 'map_route_log_id');
    }
}
