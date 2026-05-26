<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Pet;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PetRepositoryInterface
{
    public function findByUuid(string $uuid): ?Pet;

    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): Pet;

    public function update(Pet $pet, array $data): Pet;

    public function delete(Pet $pet): void;
}
