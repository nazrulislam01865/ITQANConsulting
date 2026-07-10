<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_nodes';

    protected $fillable = ['map_setting_id', 'code', 'name', 'x', 'y', 'node_type', 'is_active'];

    protected $casts = [
        'x' => 'float',
        'y' => 'float',
        'is_active' => 'boolean',
    ];

    public function mapSetting()
    {
        return $this->belongsTo(Setting::class, 'map_setting_id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'map_node_id');
    }
}
