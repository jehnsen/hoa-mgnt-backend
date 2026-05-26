<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Property;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VehicleServiceInterface
{
    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function listForProperty(string $propertyUuid, User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Vehicle;

    public function create(array $data, User $registrant): Vehicle;

    public function update(Vehicle $vehicle, array $data, User $actor): Vehicle;

    public function delete(Vehicle $vehicle): void;
}
