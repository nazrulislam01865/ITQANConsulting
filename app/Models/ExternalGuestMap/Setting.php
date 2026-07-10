<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_settings';

    protected $fillable = [
        'name', 'map_type', 'map_file', 'width', 'height', 'meters_per_pixel',
        'walk_meters_per_minute', 'buggy_meters_per_minute', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meters_per_pixel' => 'float',
        'width' => 'integer',
        'height' => 'integer',
        'walk_meters_per_minute' => 'integer',
        'buggy_meters_per_minute' => 'integer',
    ];

    public function places()
    {
        return $this->hasMany(Place::class, 'map_setting_id');
    }

    public function nodes()
    {
        return $this->hasMany(Node::class, 'map_setting_id');
    }

    public function edges()
    {
        return $this->hasMany(Edge::class, 'map_setting_id');
    }
}
