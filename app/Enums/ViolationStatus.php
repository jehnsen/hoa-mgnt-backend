<?php

declare(strict_types=1);

namespace App\Enums;

enum ViolationStatus: string
{
    case Draft    = 'draft';
    case Issued   = 'issued';
    case Appealed = 'appealed';
    case Paid     = 'paid';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match($this) {
            self::Draft    => 'Draft',
            self::Issued   => 'Issued',
            self::Appealed => 'Under Appeal',
            self::Paid     => 'Paid',
            self::Resolved => 'Resolved',
        };
    }

    /** Returns allowed next states to enforce a valid state-machine transition */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Draft    => [self::Issued],
            self::Issued   => [self::Appealed, self::Paid, self::Resolved],
            self::Appealed => [self::Issued, self::Resolved],
            self::Paid     => [self::Resolved],
            self::Resolved => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), strict: true);
    }
}
