<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function manageVotes(User $user, Meeting $meeting): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function castVote(User $user, Meeting $meeting): bool
    {
        return true;
    }
}
