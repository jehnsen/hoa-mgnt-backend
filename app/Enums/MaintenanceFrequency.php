<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceFrequency: string
{
    case Weekly    = 'weekly';
    case Monthly   = 'monthly';
    case Quarterly = 'quarterly';
    case Annually  = 'annually';

    public function label(): string
    {
        return match($this) {
            self::Weekly    => 'Weekly',
            self::Monthly   => 'Monthly',
            self::Quarterly => 'Quarterly',
            self::Annually  => 'Annually',
        };
    }
}
