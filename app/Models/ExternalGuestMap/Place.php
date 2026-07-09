<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_places';

    protected $fillable = [
        'map_setting_id', 'map_category_id', 'map_node_id', 'name', 'slug', 'subtitle',
        'description', 'icon', 'pin_number', 'x', 'y', 'lat', 'lng', 'image', 'is_qr_point',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'x' => 'float',
        'y' => 'float',
        'pin_number' => 'integer',
        'lat' => 'float',
        'lng' => 'float',
        'is_qr_point' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function mapSetting()
    {
        return $this->belongsTo(Setting::class, 'map_setting_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'map_category_id');
    }

    public function routeNode()
    {
        return $this->belongsTo(Node::class, 'map_node_id');
    }

    public function qrPoint()
    {
        return $this->hasOne(QrPoint::class, 'map_place_id');
    }
}
