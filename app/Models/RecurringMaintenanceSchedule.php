<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenanceFrequency;
use App\Enums\MaintenancePriority;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringMaintenanceSchedule extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'assigned_to',
        'category',
        'title',
        'description',
        'priority',
        'frequency',
        'frequency_interval',
        'estimated_cost',
        'next_due_at',
        'last_run_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category'           => MaintenanceCategory::class,
            'priority'           => MaintenancePriority::class,
            'frequency'          => MaintenanceFrequency::class,
            'frequency_interval' => 'integer',
            'estimated_cost'     => 'decimal:2',
            'next_due_at'        => 'date',
            'last_run_at'        => 'date',
            'is_active'          => 'boolean',
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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'recurring_schedule_id');
    }
}
