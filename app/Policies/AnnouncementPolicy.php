<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
