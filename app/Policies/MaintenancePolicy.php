<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceRequest $request): bool
    {
        if ($user->isSuperAdmin() || $user->isBoardMember()) {
            return true;
        }

        return $request->submitted_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, MaintenanceRequest $request): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
