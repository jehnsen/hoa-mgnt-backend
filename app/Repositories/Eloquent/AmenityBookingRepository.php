<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BookingStatus;
use App\Models\AmenityBooking;
use App\Repositories\Contracts\AmenityBookingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AmenityBookingRepository implements AmenityBookingRepositoryInterface
{
    public function __construct(private readonly AmenityBooking $model) {}

    public function findByUuid(string $uuid): ?AmenityBooking
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['amenity', 'property', 'booker'])
                           ->first();
    }

    public function paginateFiltered(?int $amenityId, ?int $propertyId, ?BookingStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['amenity', 'property', 'booker']);

        if ($amenityId !== null) {
            $query->where('amenity_id', $amenityId);
        }

        if ($propertyId !== null) {
            $query->where('property_id', $propertyId);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('start_at')->paginate($perPage);
    }

    public function hasConflict(int $amenityId, string $startAt, string $endAt, ?int $excludeId = null): bool
    {
        $query = $this->model->newQuery()
                             ->where('amenity_id', $amenityId)
                             ->where('status', '!=', BookingStatus::Cancelled->value)
                             ->where('start_at', '<', $endAt)
                             ->where('end_at', '>', $startAt);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function create(array $data): AmenityBooking
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(AmenityBooking $booking, array $data): AmenityBooking
    {
        $booking->fill($data)->save();

        return $booking->refresh();
    }
}
