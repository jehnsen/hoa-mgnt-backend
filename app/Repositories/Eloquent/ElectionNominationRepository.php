<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ElectionNomination;
use App\Repositories\Contracts\ElectionNominationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ElectionNominationRepository implements ElectionNominationRepositoryInterface
{
    public function __construct(private readonly ElectionNomination $model) {}

    public function findByUuid(string $uuid): ?ElectionNomination
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['election', 'nominee', 'nominator'])
                           ->first();
    }

    public function forElection(int $electionId): Collection
    {
        return $this->model->newQuery()
                           ->where('election_id', $electionId)
                           ->with(['nominee', 'nominator'])
                           ->orderBy('position_value')
                           ->get();
    }

    public function findDuplicate(int $electionId, int $nomineeId, string $positionValue): ?ElectionNomination
    {
        return $this->model->newQuery()
                           ->where('election_id', $electionId)
                           ->where('nominee_id', $nomineeId)
                           ->where('position_value', $positionValue)
                           ->first();
    }

    public function create(array $data): ElectionNomination
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(ElectionNomination $nomination, array $data): ElectionNomination
    {
        $nomination->fill($data)->save();

        return $nomination->refresh();
    }
}
