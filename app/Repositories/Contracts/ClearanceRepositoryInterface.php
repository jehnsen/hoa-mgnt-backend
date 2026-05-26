<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\ClearanceStatus;
use App\Models\Clearance;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClearanceRepositoryInterface
{
    public function findByUuid(string $uuid): ?Clearance;

    public function paginate(?Property $property, ?User $requester, ?ClearanceStatus $status, int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): Clearance;

    public function update(Clearance $clearance, array $data): Clearance;
}
