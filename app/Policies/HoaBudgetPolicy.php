<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HoaBudget;
use App\Models\User;

class HoaBudgetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, HoaBudget $budget): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, HoaBudget $budget): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
