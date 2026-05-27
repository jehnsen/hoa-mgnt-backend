<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\UtilityType;
use App\Models\UtilityMeterReading;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UtilityMeterReadingRepositoryInterface
{
    public function findByUuid(string $uuid): ?UtilityMeterReading;

    /** @return LengthAwarePaginator<UtilityMeterReading> */
    public function paginateFiltered(?int $propertyId, ?UtilityType $type, int $perPage = 20): LengthAwarePaginator;

    public function latestForProperty(int $propertyId, UtilityType $type): ?UtilityMeterReading;

    /** @param array<string, mixed> $data */
    public function create(array $data): UtilityMeterReading;

    /** @param array<string, mixed> $data */
    public function update(UtilityMeterReading $reading, array $data): UtilityMeterReading;
}
