<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'submitted_by',
        'assigned_to',
        'vendor_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'resolution_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'category'    => MaintenanceCategory::class,
            'priority'    => MaintenancePriority::class,
            'status'      => MaintenanceStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function canTransitionTo(MaintenanceStatus $next): bool
    {
        return $this->status->canTransitionTo($next);
    }

    public function scopeOpen(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereNotIn('status', [
            MaintenanceStatus::Resolved->value,
            MaintenanceStatus::Closed->value,
        ]);
    }
}
