<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RecurringMaintenanceSchedule;
use App\Models\User;

class RecurringMaintenanceSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RecurringMaintenanceSchedule $schedule): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, RecurringMaintenanceSchedule $schedule): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, RecurringMaintenanceSchedule $schedule): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
