<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UtilityMeterReading;

class UtilityMeterReadingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, UtilityMeterReading $reading): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, UtilityMeterReading $reading): bool
    {
        return $user->isSuperAdmin();
    }
}
