<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Property;

interface DelinquencyServiceInterface
{
    /** Returns true if the property has 3+ consecutive overdue monthly-dues invoices. */
    public function isDelinquent(Property $property): bool;

    /**
     * Check all active properties and update their delinquency flags.
     *
     * @return array{flagged: int, cleared: int}
     */
    public function runEscalation(): array;
}
