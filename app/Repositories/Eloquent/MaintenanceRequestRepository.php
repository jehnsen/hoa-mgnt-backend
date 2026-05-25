<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Repositories\Contracts\MaintenanceRequestRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintenanceRequestRepository implements MaintenanceRequestRepositoryInterface
{
    public function __construct(private readonly MaintenanceRequest $model) {}

    public function findByUuid(string $uuid): ?MaintenanceRequest
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'submitter', 'assignee'])
                           ->first();
    }

    public function paginateFiltered(?int $propertyId, ?MaintenanceStatus $status, ?int $submittedBy, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'submitter', 'assignee']);

        if ($propertyId !== null) {
            $query->where('property_id', $propertyId);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($submittedBy !== null) {
            $query->where('submitted_by', $submittedBy);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): MaintenanceRequest
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(MaintenanceRequest $request, array $data): MaintenanceRequest
    {
        $request->fill($data)->save();

        return $request->refresh();
    }
}
