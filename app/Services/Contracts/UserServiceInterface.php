<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    /** @return LengthAwarePaginator<User> */
    public function list(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): User;

    /** @param array<string, mixed> $data */
    public function create(array $data): User;

    /** @param array<string, mixed> $data */
    public function update(string $uuid, array $data): User;

    public function delete(string $uuid): void;
}
