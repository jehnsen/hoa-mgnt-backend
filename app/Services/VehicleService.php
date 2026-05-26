<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Services\Contracts\VehicleServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class VehicleService implements VehicleServiceInterface
{
    public function __construct(
        private readonly VehicleRepositoryInterface  $vehicleRepository,
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        if ($viewer->canManageFinancials()) {
            return $this->vehicleRepository->paginate($perPage);
        }

        // Residents see vehicles for their own properties only via property-scoped route
        return $this->vehicleRepository->paginate($perPage);
    }

    public function listForProperty(string $propertyUuid, User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        $property = $this->propertyRepository->findByUuid($propertyUuid)
            ?? throw new NotFoundHttpException("Property [{$propertyUuid}] not found.");

        $this->assertPropertyAccess($property, $viewer);

        return $this->vehicleRepository->paginateForProperty($property, $perPage);
    }

    public function findOrFail(string $uuid): Vehicle
    {
        return $this->vehicleRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Vehicle [{$uuid}] not found.");
    }

    public function create(array $data, User $registrant): Vehicle
    {
        $property = $this->propertyRepository->findByUuid($data['property_id'])
            ?? throw new NotFoundHttpException("Property [{$data['property_id']}] not found.");

        $this->assertPropertyAccess($property, $registrant);

        return DB::transaction(function () use ($data, $registrant, $property): Vehicle {
            // Only board+ can set sticker/gate-pass numbers at creation
            $allowed = $registrant->canManageFinancials()
                ? $data
                : collect($data)->except(['sticker_number', 'gate_pass_number'])->all();

            return $this->vehicleRepository->create(array_merge($allowed, [
                'property_id'   => $property->id,
                'registered_by' => $registrant->id,
            ]));
        });
    }

    public function update(Vehicle $vehicle, array $data, User $actor): Vehicle
    {
        if (! $actor->canManageFinancials()) {
            $data = collect($data)->except(['sticker_number', 'gate_pass_number', 'is_active'])->all();
        }

        return DB::transaction(fn () => $this->vehicleRepository->update($vehicle, $data));
    }

    public function delete(Vehicle $vehicle): void
    {
        DB::transaction(fn () => $this->vehicleRepository->delete($vehicle));
    }

    private function assertPropertyAccess(Property $property, User $user): void
    {
        if ($user->canManageFinancials()) {
            return;
        }

        $isResident = $user->properties()->where('properties.id', $property->id)->exists();

        if (! $isResident) {
            throw new AccessDeniedHttpException('You do not have access to this property.');
        }
    }
}
