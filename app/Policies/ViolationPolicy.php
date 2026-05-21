<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Violation;

class ViolationPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list violations
    }

    public function view(User $user, Violation $violation): bool
    {
        return true; // All authenticated users can view a single violation
    }

    public function create(User $user): bool
    {
        return $user->canManageViolations();
    }

    public function updateStatus(User $user, Violation $violation): bool
    {
        return $user->canManageViolations();
    }

    public function appendEvidence(User $user, Violation $violation): bool
    {
        return $user->canManageViolations();
    }
}
