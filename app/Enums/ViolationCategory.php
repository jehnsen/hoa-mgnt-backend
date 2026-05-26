<?php

declare(strict_types=1);

namespace App\Enums;

enum ViolationCategory: string
{
    case Parking    = 'parking';
    case Noise      = 'noise';
    case Aesthetics = 'aesthetics';
    case Garbage    = 'garbage';
    case Pets       = 'pets';
    case Structural = 'structural';
    case Other      = 'other';

    public function label(): string
    {
        return match($this) {
            self::Parking    => 'Parking',
            self::Noise      => 'Noise',
            self::Aesthetics => 'Aesthetics',
            self::Garbage    => 'Garbage',
            self::Pets       => 'Pets',
            self::Structural => 'Structural',
            self::Other      => 'Other',
        };
    }
}
