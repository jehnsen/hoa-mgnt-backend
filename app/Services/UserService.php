<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }

    public function findOrFail(string $uuid): User
    {
        $user = $this->userRepository->findByUuid($uuid);

        if ($user === null) {
            throw new NotFoundHttpException("User [{$uuid}] not found.");
        }

        return $user;
    }

    public function create(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function update(string $uuid, array $data): User
    {
        $user = $this->findOrFail($uuid);

        return $this->userRepository->update($user, $data);
    }

    public function delete(string $uuid): void
    {
        $user = $this->findOrFail($uuid);

        $this->userRepository->delete($user);
    }
}
