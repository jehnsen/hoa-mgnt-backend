<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ElectionVote;
use Illuminate\Database\Eloquent\Collection;

interface ElectionVoteRepositoryInterface
{
    public function hasVoted(int $electionId, int $voterId, string $positionValue): bool;

    /** @return Collection<int, ElectionVote> */
    public function tallyForElection(int $electionId): Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): ElectionVote;
}
