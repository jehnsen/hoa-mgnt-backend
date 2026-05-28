<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\VendorProfile;

class VendorProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VendorProfile $vendor): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, VendorProfile $vendor): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, VendorProfile $vendor): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
