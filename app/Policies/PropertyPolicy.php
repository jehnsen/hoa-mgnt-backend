<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list properties
    }

    public function view(User $user, Property $property): bool
    {
        return true; // All authenticated users can view a property
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Property $property): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->isSuperAdmin();
    }

    public function assignResident(User $user, Property $property): bool
    {
        return $user->isSuperAdmin();
    }

    public function unassignResident(User $user, Property $property): bool
    {
        return $user->isSuperAdmin();
    }
}
