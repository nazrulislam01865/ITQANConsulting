<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FooterMenuItem extends Model
{
    protected $fillable = [
        'group_key',
        'group_title',
        'label',
        'route_name',
        'url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group_key')->orderBy('sort_order')->orderBy('id');
    }
}
