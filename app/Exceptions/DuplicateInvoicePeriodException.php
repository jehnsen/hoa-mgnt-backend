<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class DuplicateInvoicePeriodException extends RuntimeException
{
    public function __construct(string $unitNumber, string $periodMonth)
    {
        parent::__construct(
            "An invoice for property [{$unitNumber}] already exists for period [{$periodMonth}]."
        );
    }
}
