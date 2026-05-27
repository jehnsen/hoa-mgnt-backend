<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\RecurringMaintenanceSchedule;
use App\Repositories\Contracts\RecurringMaintenanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class RecurringMaintenanceRepository implements RecurringMaintenanceRepositoryInterface
{
    public function __construct(private readonly RecurringMaintenanceSchedule $model) {}

    public function findByUuid(string $uuid): ?RecurringMaintenanceSchedule
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'assignee'])
                           ->first();
    }

    public function paginate(bool $activeOnly, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'assignee']);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('next_due_at')->paginate($perPage);
    }

    public function dueForRun(): Collection
    {
        return $this->model->newQuery()
                           ->where('is_active', true)
                           ->where('next_due_at', '<=', Carbon::today())
                           ->with(['property', 'assignee'])
                           ->get();
    }

    public function create(array $data): RecurringMaintenanceSchedule
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(RecurringMaintenanceSchedule $schedule, array $data): RecurringMaintenanceSchedule
    {
        $schedule->fill($data)->save();

        return $schedule->refresh();
    }

    public function delete(RecurringMaintenanceSchedule $schedule): void
    {
        $schedule->delete();
    }
}
