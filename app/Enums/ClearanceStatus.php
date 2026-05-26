<?php

declare(strict_types=1);

namespace App\Enums;

enum ClearanceStatus: string
{
    case Pending     = 'pending';
    case UnderReview = 'under_review';
    case Approved    = 'approved';
    case Issued      = 'issued';
    case Rejected    = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending     => 'Pending',
            self::UnderReview => 'Under Review',
            self::Approved    => 'Approved',
            self::Issued      => 'Issued',
            self::Rejected    => 'Rejected',
        };
    }

    /** @return array<int, self> */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Pending     => [self::UnderReview, self::Rejected],
            self::UnderReview => [self::Approved, self::Rejected],
            self::Approved    => [self::Issued],
            self::Issued,
            self::Rejected    => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), strict: true);
    }
}
