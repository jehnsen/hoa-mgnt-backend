<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendorServiceInterface
{
    public function list(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): VendorProfile;

    public function create(User $user, array $data): VendorProfile;

    public function update(VendorProfile $profile, array $data): VendorProfile;

    public function delete(VendorProfile $profile): void;

    public function assignToMaintenanceRequest(MaintenanceRequest $request, User $vendor): MaintenanceRequest;
}
