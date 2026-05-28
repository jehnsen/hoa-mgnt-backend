<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AmenityBlackout;
use App\Models\User;

class AmenityBlackoutPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AmenityBlackout $blackout): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, AmenityBlackout $blackout): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
