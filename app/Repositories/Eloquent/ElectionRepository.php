<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BoardElection;
use App\Repositories\Contracts\ElectionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ElectionRepository implements ElectionRepositoryInterface
{
    public function __construct(private readonly BoardElection $model) {}

    public function findByUuid(string $uuid): ?BoardElection
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['creator', 'nominations.nominee', 'nominations.nominator'])
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with('creator')
                           ->orderByDesc('created_at')
                           ->paginate($perPage);
    }

    public function create(array $data): BoardElection
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(BoardElection $election, array $data): BoardElection
    {
        $election->fill($data)->save();

        return $election->refresh();
    }

    public function delete(BoardElection $election): void
    {
        $election->delete();
    }
}
