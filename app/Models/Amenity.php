<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenity extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'location',
        'capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity'  => 'integer',
            'is_active' => 'boolean',
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

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('is_active', true);
    }
}
