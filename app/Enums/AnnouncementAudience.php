<?php

declare(strict_types=1);

namespace App\Enums;

enum AnnouncementAudience: string
{
    case All           = 'all';
    case Residents     = 'residents';
    case BoardMembers  = 'board_members';

    public function label(): string
    {
        return match($this) {
            self::All          => 'All Members',
            self::Residents    => 'Residents Only',
            self::BoardMembers => 'Board Members Only',
        };
    }
}
