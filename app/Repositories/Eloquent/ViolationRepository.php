<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ViolationCategory;
use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\Violation;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
                           ->with(['property', 'reporter', 'fineInvoice'])
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

    public function paginateFiltered(?ViolationStatus $status, ?int $propertyId, ?ViolationCategory $category = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'reporter']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($propertyId !== null) {
            $query->where('property_id', $propertyId);
        }

        if ($category !== null) {
            $query->where('category', $category);
        }

        return $query->orderByDesc('issued_at')->paginate($perPage);
    }

    public function repeatOffenders(int $minCount, Carbon $since, ?ViolationCategory $category = null): Collection
    {
        $subQuery = $this->model->newQuery()
            ->select('property_id', DB::raw('COUNT(*) as violation_count'))
            ->where('created_at', '>=', $since)
            ->where('status', '!=', ViolationStatus::Draft->value)
            ->groupBy('property_id')
            ->having('violation_count', '>=', $minCount);

        if ($category !== null) {
            $subQuery->where('category', $category->value);
        }

        $counts = $subQuery->get()->keyBy('property_id');

        return Property::query()
            ->whereIn('id', $counts->keys())
            ->with(['residents'])
            ->get()
            ->map(function (Property $property) use ($counts): Property {
                $property->setAttribute('violation_count', (int) $counts[$property->id]->violation_count);
                return $property;
            })
            ->sortByDesc('violation_count')
            ->values();
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
