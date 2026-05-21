<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\PropertyServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PropertyService implements PropertyServiceInterface
{
    public function __construct(
        private readonly PropertyRepositoryInterface $propertyRepository,
        private readonly UserRepositoryInterface     $userRepository,
    ) {}

    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return $this->propertyRepository->paginate($perPage);
    }

    public function findOrFail(string $uuid): Property
    {
        $property = $this->propertyRepository->findByUuid($uuid);

        if ($property === null) {
            throw new NotFoundHttpException("Property [{$uuid}] not found.");
        }

        return $property;
    }

    public function create(array $data): Property
    {
        return $this->propertyRepository->create($data);
    }

    public function update(string $uuid, array $data): Property
    {
        $property = $this->findOrFail($uuid);

        return $this->propertyRepository->update($property, $data);
    }

    public function assignResident(string $propertyUuid, string $userUuid, array $pivotData): void
    {
        $property = $this->findOrFail($propertyUuid);

        $user = $this->userRepository->findByUuid($userUuid);
        if ($user === null) {
            throw new NotFoundHttpException("User [{$userUuid}] not found.");
        }

        $this->propertyRepository->attachResident($property, $user->id, $pivotData);
    }

    public function unassignResident(string $propertyUuid, string $userUuid): void
    {
        $property = $this->findOrFail($propertyUuid);

        $user = $this->userRepository->findByUuid($userUuid);
        if ($user === null) {
            throw new NotFoundHttpException("User [{$userUuid}] not found.");
        }

        $this->propertyRepository->detachResident($property, $user->id);
    }

    public function allActive(): Collection
    {
        return $this->propertyRepository->allActive();
    }
}
