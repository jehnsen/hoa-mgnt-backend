<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\VendorProfile;
use App\Repositories\Contracts\VendorProfileRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VendorProfileRepository implements VendorProfileRepositoryInterface
{
    public function __construct(private readonly VendorProfile $model) {}

    public function findByUuid(string $uuid): ?VendorProfile
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('user')
                           ->first();
    }

    public function findByUserId(int $userId): ?VendorProfile
    {
        return $this->model->newQuery()
                           ->where('user_id', $userId)
                           ->with('user')
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with('user')
                           ->orderBy('company_name')
                           ->paginate($perPage);
    }

    public function create(array $data): VendorProfile
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(VendorProfile $profile, array $data): VendorProfile
    {
        $profile->fill($data)->save();

        return $profile->refresh();
    }

    public function delete(VendorProfile $profile): void
    {
        $profile->delete();
    }
}
