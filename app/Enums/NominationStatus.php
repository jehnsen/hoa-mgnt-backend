<?php

declare(strict_types=1);

namespace App\Enums;

enum NominationStatus: string
{
    case Pending   = 'pending';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Accepted  => 'Accepted',
            self::Rejected  => 'Rejected',
            self::Withdrawn => 'Withdrawn',
        };
    }
}
