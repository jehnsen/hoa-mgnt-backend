<?php

declare(strict_types=1);

namespace App\Enums;

enum UtilityType: string
{
    case Water       = 'water';
    case Electricity = 'electricity';
    case Gas         = 'gas';

    public function label(): string
    {
        return match($this) {
            self::Water       => 'Water',
            self::Electricity => 'Electricity',
            self::Gas         => 'Gas',
        };
    }

    public function invoiceType(): InvoiceType
    {
        return match($this) {
            self::Water       => InvoiceType::WaterBill,
            self::Electricity => InvoiceType::Utility,
            self::Gas         => InvoiceType::Utility,
        };
    }
}
