<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\VendorProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VendorProfileRepositoryInterface
{
    public function findByUuid(string $uuid): ?VendorProfile;

    public function findByUserId(int $userId): ?VendorProfile;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): VendorProfile;

    public function update(VendorProfile $profile, array $data): VendorProfile;

    public function delete(VendorProfile $profile): void;
}
