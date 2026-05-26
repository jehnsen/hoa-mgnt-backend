<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmergencyContact;
use App\Models\User;
use App\Repositories\Contracts\EmergencyContactRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\EmergencyContactServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EmergencyContactService implements EmergencyContactServiceInterface
{
    public function __construct(
        private readonly EmergencyContactRepositoryInterface $contactRepository,
        private readonly UserRepositoryInterface             $userRepository,
    ) {}

    public function listForUser(string $userUuid, User $viewer): Collection
    {
        $user = $this->userRepository->findByUuid($userUuid)
            ?? throw new NotFoundHttpException("User [{$userUuid}] not found.");

        if (! $viewer->isSuperAdmin() && $viewer->id !== $user->id) {
            throw new AccessDeniedHttpException('You can only view your own emergency contacts.');
        }

        return $this->contactRepository->allForUser($user);
    }

    public function findOrFail(string $uuid): EmergencyContact
    {
        return $this->contactRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Emergency contact [{$uuid}] not found.");
    }

    public function create(string $userUuid, array $data, User $actor): EmergencyContact
    {
        $user = $this->userRepository->findByUuid($userUuid)
            ?? throw new NotFoundHttpException("User [{$userUuid}] not found.");

        if (! $actor->isSuperAdmin() && $actor->id !== $user->id) {
            throw new AccessDeniedHttpException('You can only manage your own emergency contacts.');
        }

        return DB::transaction(fn () => $this->contactRepository->create(
            array_merge($data, ['user_id' => $user->id])
        ));
    }

    public function update(EmergencyContact $contact, array $data): EmergencyContact
    {
        return DB::transaction(fn () => $this->contactRepository->update($contact, $data));
    }

    public function delete(EmergencyContact $contact): void
    {
        DB::transaction(fn () => $this->contactRepository->delete($contact));
    }
}
