<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PropertyRepositoryInterface
{
    public function findById(int $id): ?Property;

    public function findByUuid(string $uuid): ?Property;

    public function findByUnitNumber(string $unitNumber): ?Property;

    /** @return LengthAwarePaginator<Property> */
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /** @return Collection<int, Property> */
    public function allActive(): Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): Property;

    /** @param array<string, mixed> $data */
    public function update(Property $property, array $data): Property;

    public function attachResident(Property $property, int $userId, array $pivotData): void;

    public function detachResident(Property $property, int $userId): void;
}
