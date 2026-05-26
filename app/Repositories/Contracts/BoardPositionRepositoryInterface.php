<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\BoardPositionTitle;
use App\Models\BoardPosition;
use Illuminate\Database\Eloquent\Collection;

interface BoardPositionRepositoryInterface
{
    public function findByUuid(string $uuid): ?BoardPosition;

    /** @return Collection<int, BoardPosition> */
    public function allActive(): Collection;

    public function findActiveByPosition(BoardPositionTitle $position): ?BoardPosition;

    /** @param array<string, mixed> $data */
    public function create(array $data): BoardPosition;

    /** @param array<string, mixed> $data */
    public function update(BoardPosition $position, array $data): BoardPosition;

    public function delete(BoardPosition $position): void;
}
