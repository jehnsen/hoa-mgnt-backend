<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BoardElection;
use App\Models\User;

class BoardElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BoardElection $election): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, BoardElection $election): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function transition(User $user, BoardElection $election): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function nominate(User $user, BoardElection $election): bool
    {
        return true;
    }

    public function vote(User $user, BoardElection $election): bool
    {
        return true;
    }

    public function delete(User $user, BoardElection $election): bool
    {
        return $user->isSuperAdmin();
    }
}
