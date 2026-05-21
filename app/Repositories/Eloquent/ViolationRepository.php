<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\Violation;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ViolationRepository implements ViolationRepositoryInterface
{
    public function __construct(private readonly Violation $model) {}

    public function findById(int $id): ?Violation
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Violation
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'reporter'])
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with(['property', 'reporter'])
                           ->orderByDesc('issued_at')
                           ->paginate($perPage);
    }

    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->where('property_id', $property->id)
                           ->with('reporter')
                           ->orderByDesc('created_at')
                           ->paginate($perPage);
    }

    public function paginateByStatus(ViolationStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->where('status', $status)
                           ->with(['property', 'reporter'])
                           ->orderByDesc('issued_at')
                           ->paginate($perPage);
    }

    public function paginateFiltered(?ViolationStatus $status, ?int $propertyId, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'reporter']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($propertyId !== null) {
            $query->where('property_id', $propertyId);
        }

        return $query->orderByDesc('issued_at')->paginate($perPage);
    }

    public function countActiveForProperty(Property $property): int
    {
        return $this->model->newQuery()
                           ->where('property_id', $property->id)
                           ->active()
                           ->count();
    }

    public function create(array $data): Violation
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Violation $violation, array $data): Violation
    {
        $violation->fill($data)->save();

        return $violation->refresh();
    }

    public function updateStatus(Violation $violation, ViolationStatus $status): Violation
    {
        $violation->status = $status;

        if ($status === ViolationStatus::Issued && $violation->issued_at === null) {
            $violation->issued_at = now();
        }

        if ($status === ViolationStatus::Resolved) {
            $violation->resolved_at = now();
        }

        $violation->save();

        return $violation->refresh();
    }
}
