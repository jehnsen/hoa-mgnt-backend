<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\RecurringMaintenanceSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RecurringMaintenanceRepositoryInterface
{
    public function findByUuid(string $uuid): ?RecurringMaintenanceSchedule;

    /** @return LengthAwarePaginator<RecurringMaintenanceSchedule> */
    public function paginate(bool $activeOnly, int $perPage = 20): LengthAwarePaginator;

    /** @return \Illuminate\Database\Eloquent\Collection<int, RecurringMaintenanceSchedule> */
    public function dueForRun(): \Illuminate\Database\Eloquent\Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): RecurringMaintenanceSchedule;

    /** @param array<string, mixed> $data */
    public function update(RecurringMaintenanceSchedule $schedule, array $data): RecurringMaintenanceSchedule;

    public function delete(RecurringMaintenanceSchedule $schedule): void;
}
