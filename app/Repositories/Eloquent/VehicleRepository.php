<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Property;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function __construct(private readonly Vehicle $model) {}

    public function findByUuid(string $uuid): ?Vehicle
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'registrant'])
                           ->first();
    }

    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->where('property_id', $property->id)
                           ->with('registrant')
                           ->orderByDesc('created_at')
                           ->paginate($perPage);
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with(['property', 'registrant'])
                           ->orderByDesc('created_at')
                           ->paginate($perPage);
    }

    public function create(array $data): Vehicle
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $vehicle->fill($data)->save();

        return $vehicle->refresh();
    }

    public function delete(Vehicle $vehicle): void
    {
        $vehicle->delete();
    }
}
