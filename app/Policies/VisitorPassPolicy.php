<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorPass;

class VisitorPassPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VisitorPass $pass): bool
    {
        return $user->canManageFinancials() || $user->id === $pass->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function checkIn(User $user): bool
    {
        // Gate guards (admin/board) perform check-in; residents cannot self-check-in
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, VisitorPass $pass): bool
    {
        return $user->canManageFinancials() || $user->id === $pass->resident_id;
    }
}
