<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->canManageFinancials() || $user->id === $vehicle->registered_by;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->canManageFinancials() || $user->id === $vehicle->registered_by;
    }
}
