<?php

declare(strict_types=1);

namespace App\Enums;

enum ElectionStatus: string
{
    case Draft           = 'draft';
    case NominationsOpen = 'nominations_open';
    case VotingOpen      = 'voting_open';
    case Closed          = 'closed';
    case Certified       = 'certified';

    public function label(): string
    {
        return match($this) {
            self::Draft           => 'Draft',
            self::NominationsOpen => 'Nominations Open',
            self::VotingOpen      => 'Voting Open',
            self::Closed          => 'Closed',
            self::Certified       => 'Certified',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match($this) {
            self::Draft           => $next === self::NominationsOpen,
            self::NominationsOpen => $next === self::VotingOpen,
            self::VotingOpen      => $next === self::Closed,
            self::Closed          => $next === self::Certified,
            self::Certified       => false,
        };
    }
}
