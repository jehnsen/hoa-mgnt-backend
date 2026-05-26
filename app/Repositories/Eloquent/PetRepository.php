<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Pet;
use App\Models\Property;
use App\Repositories\Contracts\PetRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PetRepository implements PetRepositoryInterface
{
    public function __construct(private readonly Pet $model) {}

    public function findByUuid(string $uuid): ?Pet
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

    public function create(array $data): Pet
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Pet $pet, array $data): Pet
    {
        $pet->fill($data)->save();

        return $pet->refresh();
    }

    public function delete(Pet $pet): void
    {
        $pet->delete();
    }
}
