<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Amenity;
use App\Models\AmenityBlackout;
use App\Repositories\Contracts\AmenityBlackoutRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AmenityBlackoutRepository implements AmenityBlackoutRepositoryInterface
{
    public function __construct(private readonly AmenityBlackout $model) {}

    public function findByUuid(string $uuid): ?AmenityBlackout
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('amenity')
                           ->first();
    }

    public function forAmenity(Amenity $amenity): Collection
    {
        return $this->model->newQuery()
                           ->where('amenity_id', $amenity->id)
                           ->orderBy('start_at')
                           ->get();
    }

    public function hasConflict(int $amenityId, string $startAt, string $endAt): bool
    {
        return $this->model->newQuery()
                           ->where('amenity_id', $amenityId)
                           ->where('start_at', '<', $endAt)
                           ->where('end_at', '>', $startAt)
                           ->exists();
    }

    public function create(array $data): AmenityBlackout
    {
        return $this->model->newQuery()->create($data);
    }

    public function delete(AmenityBlackout $blackout): void
    {
        $blackout->delete();
    }
}
