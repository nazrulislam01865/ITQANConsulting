<?php

namespace App\Models\ExternalGuestMap;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'ext_guest_map_categories';

    protected $fillable = ['name', 'slug', 'icon', 'color', 'sort_order', 'is_active'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function places()
    {
        return $this->hasMany(Place::class, 'map_category_id');
    }
}
