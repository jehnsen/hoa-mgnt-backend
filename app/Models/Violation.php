<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ViolationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Violation extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'reported_by',
        'title',
        'description',
        'status',
        'fine_amount',
        'evidence_images',
        'issued_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'status'          => ViolationStatus::class,
            'fine_amount'     => 'decimal:2',
            // JSON cast automatically encodes/decodes the geotagged images array
            'evidence_images' => 'array',
            'issued_at'       => 'datetime',
            'resolved_at'     => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereNotIn('status', [
            ViolationStatus::Paid->value,
            ViolationStatus::Resolved->value,
        ]);
    }

    // ─── Domain Helpers ───────────────────────────────────────────────────────

    public function canTransitionTo(ViolationStatus $next): bool
    {
        return $this->status->canTransitionTo($next);
    }
}
