<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;

class PetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pet $pet): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->canManageFinancials() || $user->id === $pet->registered_by;
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->canManageFinancials() || $user->id === $pet->registered_by;
    }
}
