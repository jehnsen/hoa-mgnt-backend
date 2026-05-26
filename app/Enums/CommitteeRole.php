<?php

declare(strict_types=1);

namespace App\Enums;

enum CommitteeRole: string
{
    case Chair  = 'chair';
    case Member = 'member';

    public function label(): string
    {
        return match($this) {
            self::Chair  => 'Chair',
            self::Member => 'Member',
        };
    }
}
