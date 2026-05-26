<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleType: string
{
    case Car        = 'car';
    case Motorcycle = 'motorcycle';
    case Truck      = 'truck';
    case Van        = 'van';
    case Other      = 'other';

    public function label(): string
    {
        return match($this) {
            self::Car        => 'Car',
            self::Motorcycle => 'Motorcycle',
            self::Truck      => 'Truck',
            self::Van        => 'Van',
            self::Other      => 'Other',
        };
    }
}
