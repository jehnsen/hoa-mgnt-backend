<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Submitted  = 'submitted';
    case InReview   = 'in_review';
    case InProgress = 'in_progress';
    case Resolved   = 'resolved';
    case Closed     = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Submitted  => 'Submitted',
            self::InReview   => 'In Review',
            self::InProgress => 'In Progress',
            self::Resolved   => 'Resolved',
            self::Closed     => 'Closed',
        };
    }

    /** @return array<MaintenanceStatus> */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Submitted  => [self::InReview, self::Closed],
            self::InReview   => [self::InProgress, self::Closed],
            self::InProgress => [self::Resolved, self::Closed],
            self::Resolved   => [self::Closed],
            self::Closed     => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
