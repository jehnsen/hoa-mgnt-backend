<?php

declare(strict_types=1);

namespace App\Enums;

enum BoardPositionTitle: string
{
    case President      = 'president';
    case VicePresident  = 'vice_president';
    case Treasurer      = 'treasurer';
    case Secretary      = 'secretary';
    case Auditor        = 'auditor';
    case Director       = 'director';

    public function label(): string
    {
        return match($this) {
            self::President     => 'President',
            self::VicePresident => 'Vice President',
            self::Treasurer     => 'Treasurer',
            self::Secretary     => 'Secretary',
            self::Auditor       => 'Auditor',
            self::Director      => 'Director',
        };
    }
}
