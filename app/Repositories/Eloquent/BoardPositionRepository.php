<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BoardPositionTitle;
use App\Models\BoardPosition;
use App\Repositories\Contracts\BoardPositionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BoardPositionRepository implements BoardPositionRepositoryInterface
{
    public function __construct(private readonly BoardPosition $model) {}

    public function findByUuid(string $uuid): ?BoardPosition
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('user')
                           ->first();
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()
                           ->where('is_active', true)
                           ->with('user')
                           ->orderBy('position')
                           ->get();
    }

    public function findActiveByPosition(BoardPositionTitle $position): ?BoardPosition
    {
        return $this->model->newQuery()
                           ->where('position', $position)
                           ->where('is_active', true)
                           ->first();
    }

    public function create(array $data): BoardPosition
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(BoardPosition $position, array $data): BoardPosition
    {
        $position->fill($data)->save();

        return $position->refresh();
    }

    public function delete(BoardPosition $position): void
    {
        $position->delete();
    }
}
