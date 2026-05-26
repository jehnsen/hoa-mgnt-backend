<?php

declare(strict_types=1);

namespace App\Enums;

enum ClearancePurpose: string
{
    case Sale    = 'sale';
    case Rental  = 'rental';
    case General = 'general';

    public function label(): string
    {
        return match($this) {
            self::Sale    => 'Property Sale',
            self::Rental  => 'Rental',
            self::General => 'General Purpose',
        };
    }
}
