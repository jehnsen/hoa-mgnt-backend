<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\ClearanceStatus;
use App\Models\Clearance;
use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\ClearanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClearanceRepository implements ClearanceRepositoryInterface
{
    public function __construct(private readonly Clearance $model) {}

    public function findByUuid(string $uuid): ?Clearance
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'requester', 'issuer'])
                           ->first();
    }

    public function paginate(?Property $property, ?User $requester, ?ClearanceStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'requester', 'issuer']);

        if ($property !== null) {
            $query->where('property_id', $property->id);
        }

        if ($requester !== null) {
            $query->where('requested_by', $requester->id);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): Clearance
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Clearance $clearance, array $data): Clearance
    {
        $clearance->fill($data)->save();

        return $clearance->refresh();
    }
}
