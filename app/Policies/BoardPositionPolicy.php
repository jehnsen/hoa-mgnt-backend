<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BoardPosition;
use App\Models\User;

class BoardPositionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BoardPosition $position): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, BoardPosition $position): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, BoardPosition $position): bool
    {
        return $user->isSuperAdmin();
    }
}
