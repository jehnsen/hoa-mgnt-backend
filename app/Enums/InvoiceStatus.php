<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Overdue   = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Paid      => 'Paid',
            self::Overdue   => 'Overdue',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isSettleable(): bool
    {
        return in_array($this, [self::Pending, self::Overdue], strict: true);
    }
}
