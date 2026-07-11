<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'need',
        'areas',
        'support_types',
        'budget_range',
        'preferred_contact_method',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'areas' => 'array',
        'support_types' => 'array',
    ];

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('created_at')->latest('id');
    }

    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }
}
