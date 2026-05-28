<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'property_id',
        'type',
        'description',
        'base_amount',
        'late_fee_amount',
        'status',
        'due_at',
        'period_month',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'type'            => InvoiceType::class,
            'base_amount'     => 'decimal:2',
            'late_fee_amount' => 'decimal:2',
            // total_amount is a MySQL stored generated column — no cast needed; DB returns it as-is
            'status'          => InvoiceStatus::class,
            'due_at'          => 'date',
            'period_month'    => 'date',
            'paid_at'         => 'datetime',
        ];
    }

    /** The uniqueIds method tells Laravel which columns hold UUID values */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('status', InvoiceStatus::Pending);
    }

    public function scopeOverdue(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('status', InvoiceStatus::Overdue);
    }

    // ─── Domain Helpers ───────────────────────────────────────────────────────

    public function isSettleable(): bool
    {
        return $this->status->isSettleable();
    }

    /** Convenience accessor so PHP code can read total_amount when the stored column is not yet persisted. */
    public function getComputedTotalAttribute(): float
    {
        return (float) $this->base_amount + (float) $this->late_fee_amount;
    }
}
