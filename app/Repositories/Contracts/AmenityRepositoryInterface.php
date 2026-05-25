<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Amenity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AmenityRepositoryInterface
{
    public function findByUuid(string $uuid): ?Amenity;

    /** @return LengthAwarePaginator<Amenity> */
    public function paginate(bool $activeOnly, int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): Amenity;

    /** @param array<string, mixed> $data */
    public function update(Amenity $amenity, array $data): Amenity;
}
