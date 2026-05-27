<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\MaintenanceRequest;
use App\Models\RecurringMaintenanceSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RecurringMaintenanceServiceInterface
{
    /** @return LengthAwarePaginator<RecurringMaintenanceSchedule> */
    public function list(bool $activeOnly = false, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): RecurringMaintenanceSchedule;

    /** @param array<string, mixed> $data */
    public function create(array $data): RecurringMaintenanceSchedule;

    /** @param array<string, mixed> $data */
    public function update(RecurringMaintenanceSchedule $schedule, array $data): RecurringMaintenanceSchedule;

    public function delete(RecurringMaintenanceSchedule $schedule): void;

    /** Manually spawn a maintenance request from a schedule and advance next_due_at. */
    public function spawn(RecurringMaintenanceSchedule $schedule): MaintenanceRequest;
}
