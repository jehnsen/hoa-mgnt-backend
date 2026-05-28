<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'location',
        'capacity',
        'fee_per_hour',
        'security_deposit',
        'monthly_booking_limit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity'              => 'integer',
            'fee_per_hour'          => 'decimal:2',
            'security_deposit'      => 'decimal:2',
            'monthly_booking_limit' => 'integer',
            'is_active'             => 'boolean',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(AmenityBooking::class);
    }

    public function blackouts(): HasMany
    {
        return $this->hasMany(AmenityBlackout::class);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_active', true);
    }
}
