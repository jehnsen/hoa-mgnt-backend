<?php

declare(strict_types=1);

namespace App\Enums;

enum BudgetCategory: string
{
    case Maintenance    = 'maintenance';
    case Utilities      = 'utilities';
    case Amenities      = 'amenities';
    case Administrative = 'administrative';
    case Landscaping    = 'landscaping';
    case Security       = 'security';
    case Contingency    = 'contingency';
    case Other          = 'other';

    public function label(): string
    {
        return match($this) {
            self::Maintenance    => 'Maintenance',
            self::Utilities      => 'Utilities',
            self::Amenities      => 'Amenities',
            self::Administrative => 'Administrative',
            self::Landscaping    => 'Landscaping',
            self::Security       => 'Security',
            self::Contingency    => 'Contingency',
            self::Other          => 'Other',
        };
    }
}
