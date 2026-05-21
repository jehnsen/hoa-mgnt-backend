<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasUuids, SoftDeletes;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $fillable = [
        'unit_number',
        'block',
        'floor',
        'street_address',
        'type',
        'monthly_dues',
        'late_fee_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'floor'         => 'integer',
            'monthly_dues'  => 'decimal:2',
            'late_fee_rate' => 'decimal:4',
            'is_active'     => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_user')
                    ->withPivot(['move_in_at', 'move_out_at', 'is_primary_resident'])
                    ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_active', true);
    }
}
