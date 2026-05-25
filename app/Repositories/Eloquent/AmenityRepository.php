<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Amenity;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AmenityRepository implements AmenityRepositoryInterface
{
    public function __construct(private readonly Amenity $model) {}

    public function findByUuid(string $uuid): ?Amenity
    {
        return $this->model->newQuery()->where('uuid', $uuid)->first();
    }

    public function paginate(bool $activeOnly, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->orderBy('name');

        if ($activeOnly) {
            $query->active();
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Amenity
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Amenity $amenity, array $data): Amenity
    {
        $amenity->fill($data)->save();

        return $amenity->refresh();
    }
}
