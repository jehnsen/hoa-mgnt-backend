<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Property;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function __construct(private readonly Property $model) {}

    public function findById(int $id): ?Property
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Property
    {
        return $this->model->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByUnitNumber(string $unitNumber): ?Property
    {
        return $this->model->newQuery()
                           ->where('unit_number', $unitNumber)
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with('residents')
                           ->orderBy('unit_number')
                           ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()
                           ->active()
                           ->orderBy('unit_number')
                           ->get();
    }

    public function create(array $data): Property
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Property $property, array $data): Property
    {
        $property->fill($data)->save();

        return $property->refresh();
    }

    public function attachResident(Property $property, int $userId, array $pivotData): void
    {
        // syncWithoutDetaching keeps existing residents intact when adding a new one
        $property->residents()->syncWithoutDetaching([$userId => $pivotData]);
    }

    public function detachResident(Property $property, int $userId): void
    {
        $property->residents()->detach($userId);
    }
}
