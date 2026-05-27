<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Amenity;
use App\Models\AmenityBlackout;
use Illuminate\Database\Eloquent\Collection;

interface AmenityBlackoutServiceInterface
{
    /** @return Collection<int, AmenityBlackout> */
    public function forAmenity(Amenity $amenity): Collection;

    public function findOrFail(string $uuid): AmenityBlackout;

    /** @param array<string, mixed> $data */
    public function create(Amenity $amenity, array $data): AmenityBlackout;

    public function delete(AmenityBlackout $blackout): void;
}
