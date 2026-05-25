<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AmenityBooking;
use App\Models\User;

class AmenityBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AmenityBooking $booking): bool
    {
        if ($user->isSuperAdmin() || $user->isBoardMember()) {
            return true;
        }

        return $booking->booked_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function updateStatus(User $user, AmenityBooking $booking): bool
    {
        if ($user->isSuperAdmin() || $user->isBoardMember()) {
            return true;
        }

        // Resident can cancel their own booking
        return $booking->booked_by === $user->id;
    }
}
