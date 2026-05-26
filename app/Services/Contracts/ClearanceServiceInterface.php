<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ClearanceStatus;
use App\Models\Clearance;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClearanceServiceInterface
{
    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Clearance;

    public function create(array $data, User $requester): Clearance;

    public function updateStatus(Clearance $clearance, ClearanceStatus $newStatus, array $data, User $actor): Clearance;
}
