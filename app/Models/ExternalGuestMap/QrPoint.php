<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrPoint extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_qr_points';

    protected $fillable = ['map_place_id', 'title', 'qr_code', 'qr_url', 'printed_label', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'map_place_id');
    }
}
