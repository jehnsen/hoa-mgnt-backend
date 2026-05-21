<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $model) {}

    public function findById(int $id): ?User
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->model->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()
                           ->where('email', $email)
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->orderBy('name')
                           ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
