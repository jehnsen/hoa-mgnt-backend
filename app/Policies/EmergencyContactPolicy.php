<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EmergencyContact $contact): bool
    {
        return $user->canManageFinancials() || $user->id === $contact->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EmergencyContact $contact): bool
    {
        return $user->canManageFinancials() || $user->id === $contact->user_id;
    }

    public function delete(User $user, EmergencyContact $contact): bool
    {
        return $user->canManageFinancials() || $user->id === $contact->user_id;
    }
}
