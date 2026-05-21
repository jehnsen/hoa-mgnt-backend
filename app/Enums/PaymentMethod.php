<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash         = 'cash';
    case Check        = 'check';
    case BankTransfer = 'bank_transfer';
    case CreditCard   = 'credit_card';
    case Online       = 'online';

    public function label(): string
    {
        return match($this) {
            self::Cash         => 'Cash',
            self::Check        => 'Check',
            self::BankTransfer => 'Bank Transfer',
            self::CreditCard   => 'Credit Card',
            self::Online       => 'Online Payment',
        };
    }
}
