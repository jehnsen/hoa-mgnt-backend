<?php

declare(strict_types=1);

namespace App\Enums;

enum MeetingStatus: string
{
    case Scheduled = 'scheduled';
    case Ongoing   = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Scheduled => 'Scheduled',
            self::Ongoing   => 'Ongoing',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /** @return array<MeetingStatus> */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Scheduled => [self::Ongoing, self::Cancelled],
            self::Ongoing   => [self::Completed, self::Cancelled],
            self::Completed => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
