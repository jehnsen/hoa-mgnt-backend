<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Invoice;
use RuntimeException;

final class InvoiceNotCancellableException extends RuntimeException
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct(
            "Invoice [{$invoice->uuid}] has status [{$invoice->status->value}] and cannot be cancelled."
        );
    }
}
