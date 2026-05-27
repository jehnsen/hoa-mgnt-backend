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

// vendor() is BelongsTo VendorProfile — FK references vendor_profiles on MySQL (users on SQLite dev)

class MaintenanceRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'submitted_by',
        'assigned_to',
        'vendor_id',
        'recurring_schedule_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'resolution_notes',
        'estimated_cost',
        'actual_cost',
        'photos',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'category'       => MaintenanceCategory::class,
            'priority'       => MaintenancePriority::class,
            'status'         => MaintenanceStatus::class,
            'estimated_cost' => 'decimal:2',
            'actual_cost'    => 'decimal:2',
            'photos'         => 'array',
            'resolved_at'    => 'datetime',
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
        return $this->belongsTo(VendorProfile::class, 'vendor_id');
    }

    public function recurringSchedule(): BelongsTo
    {
        return $this->belongsTo(RecurringMaintenanceSchedule::class, 'recurring_schedule_id');
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
