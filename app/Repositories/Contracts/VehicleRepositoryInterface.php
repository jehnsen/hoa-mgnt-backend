<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Property;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface
{
    public function findByUuid(string $uuid): ?Vehicle;

    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): Vehicle;

    public function update(Vehicle $vehicle, array $data): Vehicle;

    public function delete(Vehicle $vehicle): void;
}
