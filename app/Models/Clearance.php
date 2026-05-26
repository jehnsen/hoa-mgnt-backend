<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClearancePurpose;
use App\Enums\ClearanceStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clearance extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'requested_by',
        'issued_by',
        'purpose',
        'status',
        'notes',
        'rejection_reason',
        'valid_until',
        'issued_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'purpose'     => ClearancePurpose::class,
            'status'      => ClearanceStatus::class,
            'valid_until' => 'date',
            'issued_at'   => 'datetime',
            'rejected_at' => 'datetime',
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

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function canTransitionTo(ClearanceStatus $next): bool
    {
        return $this->status->canTransitionTo($next);
    }
}
