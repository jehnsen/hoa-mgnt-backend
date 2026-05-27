<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\UtilityType;
use App\Models\UtilityMeterReading;
use App\Repositories\Contracts\UtilityMeterReadingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UtilityMeterReadingRepository implements UtilityMeterReadingRepositoryInterface
{
    public function __construct(private readonly UtilityMeterReading $model) {}

    public function findByUuid(string $uuid): ?UtilityMeterReading
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['property', 'reader', 'invoice'])
                           ->first();
    }

    public function paginateFiltered(?int $propertyId, ?UtilityType $type, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['property', 'reader']);

        if ($propertyId !== null) {
            $query->where('property_id', $propertyId);
        }

        if ($type !== null) {
            $query->where('utility_type', $type);
        }

        return $query->orderByDesc('reading_date')->paginate($perPage);
    }

    public function latestForProperty(int $propertyId, UtilityType $type): ?UtilityMeterReading
    {
        return $this->model->newQuery()
                           ->where('property_id', $propertyId)
                           ->where('utility_type', $type)
                           ->orderByDesc('reading_date')
                           ->first();
    }

    public function create(array $data): UtilityMeterReading
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(UtilityMeterReading $reading, array $data): UtilityMeterReading
    {
        $reading->fill($data)->save();

        return $reading->refresh();
    }
}
