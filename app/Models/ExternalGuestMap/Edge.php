<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edge extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_edges';

    protected $fillable = [
        'map_setting_id', 'from_node_id', 'to_node_id', 'path_points', 'distance_meters',
        'walk_enabled', 'buggy_enabled', 'staff_only', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'path_points' => 'array',
        'distance_meters' => 'float',
        'walk_enabled' => 'boolean',
        'buggy_enabled' => 'boolean',
        'staff_only' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function mapSetting()
    {
        return $this->belongsTo(Setting::class, 'map_setting_id');
    }

    public function fromNode()
    {
        return $this->belongsTo(Node::class, 'from_node_id');
    }

    public function toNode()
    {
        return $this->belongsTo(Node::class, 'to_node_id');
    }
}
