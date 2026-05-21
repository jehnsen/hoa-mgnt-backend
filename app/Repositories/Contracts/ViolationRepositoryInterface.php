<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ViolationStatus;
use App\Models\Property;
use App\Models\Violation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ViolationRepositoryInterface
{
    public function findById(int $id): ?Violation;

    public function findByUuid(string $uuid): ?Violation;

    /** @return LengthAwarePaginator<Violation> */
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Violation> */
    public function paginateForProperty(Property $property, int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Violation> */
    public function paginateByStatus(ViolationStatus $status, int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Violation> */
    public function paginateFiltered(?ViolationStatus $status, ?int $propertyId, int $perPage = 20): LengthAwarePaginator;

    /** Count active (non-resolved) violations for a given property */
    public function countActiveForProperty(Property $property): int;

    /** @param array<string, mixed> $data */
    public function create(array $data): Violation;

    /** @param array<string, mixed> $data */
    public function update(Violation $violation, array $data): Violation;

    public function updateStatus(Violation $violation, ViolationStatus $status): Violation;
}
