<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MaintenanceRequestRepositoryInterface
{
    public function findByUuid(string $uuid): ?MaintenanceRequest;

    /** @return LengthAwarePaginator<MaintenanceRequest> */
    public function paginateFiltered(?int $propertyId, ?MaintenanceStatus $status, ?int $submittedBy, int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): MaintenanceRequest;

    /** @param array<string, mixed> $data */
    public function update(MaintenanceRequest $request, array $data): MaintenanceRequest;
}
