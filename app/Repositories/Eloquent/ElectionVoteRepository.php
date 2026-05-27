<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ElectionVote;
use App\Repositories\Contracts\ElectionVoteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ElectionVoteRepository implements ElectionVoteRepositoryInterface
{
    public function __construct(private readonly ElectionVote $model) {}

    public function hasVoted(int $electionId, int $voterId, string $positionValue): bool
    {
        return $this->model->newQuery()
                           ->where('election_id', $electionId)
                           ->where('voter_id', $voterId)
                           ->where('position_value', $positionValue)
                           ->exists();
    }

    public function tallyForElection(int $electionId): Collection
    {
        return $this->model->newQuery()
                           ->where('election_id', $electionId)
                           ->with('nomination.nominee')
                           ->get();
    }

    public function create(array $data): ElectionVote
    {
        return $this->model->newQuery()->create($data);
    }
}
