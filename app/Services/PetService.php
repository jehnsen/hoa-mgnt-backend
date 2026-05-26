<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pet;
use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\PetRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\Contracts\PetServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PetService implements PetServiceInterface
{
    public function __construct(
        private readonly PetRepositoryInterface      $petRepository,
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        return $this->petRepository->paginate($perPage);
    }

    public function listForProperty(string $propertyUuid, User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        $property = $this->propertyRepository->findByUuid($propertyUuid)
            ?? throw new NotFoundHttpException("Property [{$propertyUuid}] not found.");

        $this->assertPropertyAccess($property, $viewer);

        return $this->petRepository->paginateForProperty($property, $perPage);
    }

    public function findOrFail(string $uuid): Pet
    {
        return $this->petRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Pet [{$uuid}] not found.");
    }

    public function create(array $data, User $registrant): Pet
    {
        $property = $this->propertyRepository->findByUuid($data['property_id'])
            ?? throw new NotFoundHttpException("Property [{$data['property_id']}] not found.");

        $this->assertPropertyAccess($property, $registrant);

        return DB::transaction(fn () => $this->petRepository->create(array_merge($data, [
            'property_id'   => $property->id,
            'registered_by' => $registrant->id,
        ])));
    }

    public function update(Pet $pet, array $data): Pet
    {
        return DB::transaction(fn () => $this->petRepository->update($pet, $data));
    }

    public function delete(Pet $pet): void
    {
        DB::transaction(fn () => $this->petRepository->delete($pet));
    }

    private function assertPropertyAccess(Property $property, User $user): void
    {
        if ($user->canManageFinancials()) {
            return;
        }

        if (! $user->properties()->where('properties.id', $property->id)->exists()) {
            throw new AccessDeniedHttpException('You do not have access to this property.');
        }
    }
}
