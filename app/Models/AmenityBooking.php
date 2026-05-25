<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmenityBooking extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'amenity_id',
        'property_id',
        'booked_by',
        'title',
        'start_at',
        'end_at',
        'status',
        'notes',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'status'       => BookingStatus::class,
            'start_at'     => 'datetime',
            'end_at'       => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where('status', '!=', BookingStatus::Cancelled->value);
    }
}
