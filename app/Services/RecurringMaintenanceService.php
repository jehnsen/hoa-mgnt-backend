<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MaintenanceFrequency;
use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\RecurringMaintenanceSchedule;
use App\Repositories\Contracts\MaintenanceRequestRepositoryInterface;
use App\Repositories\Contracts\RecurringMaintenanceRepositoryInterface;
use App\Services\Contracts\RecurringMaintenanceServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RecurringMaintenanceService implements RecurringMaintenanceServiceInterface
{
    public function __construct(
        private readonly RecurringMaintenanceRepositoryInterface $scheduleRepository,
        private readonly MaintenanceRequestRepositoryInterface   $maintenanceRepository,
    ) {}

    public function list(bool $activeOnly = false, int $perPage = 20): LengthAwarePaginator
    {
        return $this->scheduleRepository->paginate($activeOnly, $perPage);
    }

    public function findOrFail(string $uuid): RecurringMaintenanceSchedule
    {
        $schedule = $this->scheduleRepository->findByUuid($uuid);

        if ($schedule === null) {
            throw new NotFoundHttpException("Recurring schedule [{$uuid}] not found.");
        }

        return $schedule;
    }

    public function create(array $data): RecurringMaintenanceSchedule
    {
        return DB::transaction(function () use ($data): RecurringMaintenanceSchedule {
            return $this->scheduleRepository->create([
                'property_id'        => $data['property_id'] ?? null,
                'assigned_to'        => $data['assigned_to'] ?? null,
                'category'           => $data['category'],
                'title'              => $data['title'],
                'description'        => $data['description'],
                'priority'           => $data['priority'] ?? 'normal',
                'frequency'          => $data['frequency'],
                'frequency_interval' => $data['frequency_interval'] ?? 1,
                'estimated_cost'     => $data['estimated_cost'] ?? null,
                'next_due_at'        => $data['next_due_at'],
                'is_active'          => $data['is_active'] ?? true,
            ]);
        });
    }

    public function update(RecurringMaintenanceSchedule $schedule, array $data): RecurringMaintenanceSchedule
    {
        return $this->scheduleRepository->update($schedule, $data);
    }

    public function delete(RecurringMaintenanceSchedule $schedule): void
    {
        $this->scheduleRepository->delete($schedule);
    }

    public function spawn(RecurringMaintenanceSchedule $schedule): MaintenanceRequest
    {
        return DB::transaction(function () use ($schedule): MaintenanceRequest {
            $request = $this->maintenanceRepository->create([
                'property_id'           => $schedule->property_id,
                'recurring_schedule_id' => $schedule->id,
                'assigned_to'           => $schedule->assigned_to,
                'category'              => $schedule->category,
                'title'                 => $schedule->title,
                'description'           => $schedule->description,
                'priority'              => $schedule->priority,
                'status'                => MaintenanceStatus::Submitted,
                'estimated_cost'        => $schedule->estimated_cost,
            ]);

            $nextDueAt = $this->computeNextDueAt($schedule);

            $this->scheduleRepository->update($schedule, [
                'last_run_at' => Carbon::today(),
                'next_due_at' => $nextDueAt,
            ]);

            return $request;
        });
    }

    private function computeNextDueAt(RecurringMaintenanceSchedule $schedule): Carbon
    {
        $interval = $schedule->frequency_interval;

        return match($schedule->frequency) {
            MaintenanceFrequency::Weekly    => $schedule->next_due_at->copy()->addWeeks($interval),
            MaintenanceFrequency::Monthly   => $schedule->next_due_at->copy()->addMonths($interval),
            MaintenanceFrequency::Quarterly => $schedule->next_due_at->copy()->addMonths(3 * $interval),
            MaintenanceFrequency::Annually  => $schedule->next_due_at->copy()->addYears($interval),
        };
    }
}
