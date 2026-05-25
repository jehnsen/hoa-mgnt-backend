<?php

declare(strict_types=1);

namespace App\Enums;

enum MaintenanceCategory: string
{
    case Plumbing    = 'plumbing';
    case Electrical  = 'electrical';
    case Hvac        = 'hvac';
    case Structural  = 'structural';
    case Landscaping = 'landscaping';
    case CommonArea  = 'common_area';
    case Other       = 'other';

    public function label(): string
    {
        return match($this) {
            self::Plumbing    => 'Plumbing',
            self::Electrical  => 'Electrical',
            self::Hvac        => 'HVAC',
            self::Structural  => 'Structural',
            self::Landscaping => 'Landscaping',
            self::CommonArea  => 'Common Area',
            self::Other       => 'Other',
        };
    }
}
