<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BoardElection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ElectionRepositoryInterface
{
    public function findByUuid(string $uuid): ?BoardElection;

    /** @return LengthAwarePaginator<BoardElection> */
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): BoardElection;

    /** @param array<string, mixed> $data */
    public function update(BoardElection $election, array $data): BoardElection;

    public function delete(BoardElection $election): void;
}
