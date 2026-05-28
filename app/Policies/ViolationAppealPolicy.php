<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\ViolationAppeal;

class ViolationAppealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ViolationAppeal $appeal): bool
    {
        return $user->canManageViolations() || $user->id === $appeal->appellant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, ViolationAppeal $appeal): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
