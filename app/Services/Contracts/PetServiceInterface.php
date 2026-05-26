<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PetServiceInterface
{
    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function listForProperty(string $propertyUuid, User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Pet;

    public function create(array $data, User $registrant): Pet;

    public function update(Pet $pet, array $data): Pet;

    public function delete(Pet $pet): void;
}
