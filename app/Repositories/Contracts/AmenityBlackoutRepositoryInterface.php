<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Amenity;
use App\Models\AmenityBlackout;
use Illuminate\Database\Eloquent\Collection;

interface AmenityBlackoutRepositoryInterface
{
    public function findByUuid(string $uuid): ?AmenityBlackout;

    /** @return Collection<int, AmenityBlackout> */
    public function forAmenity(Amenity $amenity): Collection;

    public function hasConflict(int $amenityId, string $startAt, string $endAt): bool;

    /** @param array<string, mixed> $data */
    public function create(array $data): AmenityBlackout;

    public function delete(AmenityBlackout $blackout): void;
}
