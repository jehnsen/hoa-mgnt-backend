<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\VendorProfile;
use App\Repositories\Contracts\VendorProfileRepositoryInterface;
use App\Services\Contracts\VendorServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class VendorService implements VendorServiceInterface
{
    public function __construct(
        private readonly VendorProfileRepositoryInterface $vendorProfileRepository,
    ) {}

    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return $this->vendorProfileRepository->paginate($perPage);
    }

    public function findOrFail(string $uuid): VendorProfile
    {
        $profile = $this->vendorProfileRepository->findByUuid($uuid);

        if ($profile === null) {
            throw new NotFoundHttpException("Vendor profile [{$uuid}] not found.");
        }

        return $profile;
    }

    public function create(User $user, array $data): VendorProfile
    {
        if (! $user->isVendor()) {
            throw new UnprocessableEntityHttpException("User [{$user->uuid}] does not have the vendor role.");
        }

        if ($this->vendorProfileRepository->findByUserId($user->id) !== null) {
            throw new UnprocessableEntityHttpException("A vendor profile already exists for this user.");
        }

        return DB::transaction(function () use ($user, $data): VendorProfile {
            return $this->vendorProfileRepository->create(array_merge($data, ['user_id' => $user->id]));
        });
    }

    public function update(VendorProfile $profile, array $data): VendorProfile
    {
        return DB::transaction(function () use ($profile, $data): VendorProfile {
            return $this->vendorProfileRepository->update($profile, $data);
        });
    }

    public function delete(VendorProfile $profile): void
    {
        DB::transaction(function () use ($profile): void {
            $this->vendorProfileRepository->delete($profile);
        });
    }

    public function assignToMaintenanceRequest(MaintenanceRequest $request, User $vendor): MaintenanceRequest
    {
        if (! $vendor->isVendor()) {
            throw new UnprocessableEntityHttpException("User [{$vendor->uuid}] does not have the vendor role.");
        }

        return DB::transaction(function () use ($request, $vendor): MaintenanceRequest {
            $request->vendor_id = $vendor->id;
            $request->save();

            return $request->refresh();
        });
    }
}
