<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Committee;
use App\Models\User;

class CommitteePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Committee $committee): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, Committee $committee): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function manageMember(User $user, Committee $committee): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, Committee $committee): bool
    {
        return $user->isSuperAdmin();
    }
}
