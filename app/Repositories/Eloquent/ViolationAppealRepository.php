<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Violation;
use App\Models\ViolationAppeal;
use App\Repositories\Contracts\ViolationAppealRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ViolationAppealRepository implements ViolationAppealRepositoryInterface
{
    public function __construct(private readonly ViolationAppeal $model) {}

    public function findByUuid(string $uuid): ?ViolationAppeal
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['violation', 'appellant'])
                           ->first();
    }

    public function forViolation(Violation $violation): Collection
    {
        return $this->model->newQuery()
                           ->where('violation_id', $violation->id)
                           ->with('appellant')
                           ->orderByDesc('created_at')
                           ->get();
    }

    public function create(array $data): ViolationAppeal
    {
        return $this->model->newQuery()->create($data);
    }
}
