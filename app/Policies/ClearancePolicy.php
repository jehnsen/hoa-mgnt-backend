<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Clearance;
use App\Models\User;

class ClearancePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Clearance $clearance): bool
    {
        return $user->canManageFinancials() || $user->id === $clearance->requested_by;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, Clearance $clearance): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, Clearance $clearance): bool
    {
        return $user->isSuperAdmin();
    }
}
