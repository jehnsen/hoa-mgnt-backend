<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\BoardPosition;
use Illuminate\Database\Eloquent\Collection;

interface BoardServiceInterface
{
    /** @return Collection<int, BoardPosition> */
    public function listActive(): Collection;

    public function findOrFail(string $uuid): BoardPosition;

    /**
     * Assign a user to a board position. Throws 409 if position is already occupied.
     *
     * @param array<string, mixed> $data
     */
    public function assign(array $data): BoardPosition;

    /** @param array<string, mixed> $data */
    public function update(BoardPosition $position, array $data): BoardPosition;

    public function vacate(BoardPosition $position): BoardPosition;

    public function delete(BoardPosition $position): void;
}
