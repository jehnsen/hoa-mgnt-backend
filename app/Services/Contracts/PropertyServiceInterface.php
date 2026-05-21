<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PropertyServiceInterface
{
    /** @return LengthAwarePaginator<Property> */
    public function list(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Property;

    /** @param array<string, mixed> $data */
    public function create(array $data): Property;

    /** @param array<string, mixed> $data */
    public function update(string $uuid, array $data): Property;

    /** @param array<string, mixed> $pivotData */
    public function assignResident(string $propertyUuid, string $userUuid, array $pivotData): void;

    public function unassignResident(string $propertyUuid, string $userUuid): void;

    /** @return Collection<int, Property> */
    public function allActive(): Collection;
}
