<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\ElectionNomination;
use Illuminate\Database\Eloquent\Collection;

interface ElectionNominationRepositoryInterface
{
    public function findByUuid(string $uuid): ?ElectionNomination;

    /** @return Collection<int, ElectionNomination> */
    public function forElection(int $electionId): Collection;

    public function findDuplicate(int $electionId, int $nomineeId, string $positionValue): ?ElectionNomination;

    /** @param array<string, mixed> $data */
    public function create(array $data): ElectionNomination;

    /** @param array<string, mixed> $data */
    public function update(ElectionNomination $nomination, array $data): ElectionNomination;
}
