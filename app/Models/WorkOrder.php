<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'reviewing' => 'Reviewing',
        'contacted' => 'Contacted',
        'quoted' => 'Quoted',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
        'completed' => 'Completed',
    ];

    protected $fillable = [
        'reference_number',
        'page_section_item_id',
        'work_key',
        'work_title',
        'work_category',
        'customer_name',
        'company_name',
        'email',
        'phone',
        'preferred_contact_method',
        'budget_range',
        'timeline',
        'project_summary',
        'requirements',
        'status',
        'internal_notes',
        'viewed_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (WorkOrder $order): void {
            if (filled($order->reference_number)) {
                return;
            }

            $order->forceFill([
                'reference_number' => sprintf(
                    'ITQ-WO-%s-%05d',
                    $order->created_at?->format('ymd') ?? now()->format('ymd'),
                    $order->getKey()
                ),
            ])->saveQuietly();
        });
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(PageSectionItem::class, 'page_section_item_id');
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('created_at')->latest('id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function isUnviewed(): bool
    {
        return $this->viewed_at === null;
    }
}
