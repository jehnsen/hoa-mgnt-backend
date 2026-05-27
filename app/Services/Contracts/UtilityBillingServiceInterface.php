<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;
use App\Models\UtilityMeterReading;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UtilityBillingServiceInterface
{
    /** @return LengthAwarePaginator<UtilityMeterReading> */
    public function list(?string $propertyUuid, ?string $utilityType, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): UtilityMeterReading;

    /** @param array<string, mixed> $data */
    public function recordReading(Property $property, array $data, User $reader): UtilityMeterReading;

    public function generateBill(UtilityMeterReading $reading): Invoice;
}
