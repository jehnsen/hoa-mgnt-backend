<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorPass extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'property_id',
        'resident_id',
        'visitor_name',
        'vehicle_plate',
        'expected_at',
        'expires_at',
        'purpose',
        'access_code',
        'is_used',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_at' => 'datetime',
            'expires_at'  => 'datetime',
            'is_used'     => 'boolean',
            'used_at'     => 'datetime',
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

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
