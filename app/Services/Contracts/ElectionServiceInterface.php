<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ElectionStatus;
use App\Enums\NominationStatus;
use App\Models\BoardElection;
use App\Models\ElectionNomination;
use App\Models\ElectionVote;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ElectionServiceInterface
{
    /** @return LengthAwarePaginator<BoardElection> */
    public function list(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): BoardElection;

    /** @param array<string, mixed> $data */
    public function create(array $data, User $creator): BoardElection;

    /** @param array<string, mixed> $data */
    public function update(BoardElection $election, array $data): BoardElection;

    public function transition(BoardElection $election, ElectionStatus $newStatus): BoardElection;

    public function delete(BoardElection $election): void;

    /** @param array<string, mixed> $data */
    public function nominate(BoardElection $election, User $nominee, User $nominatedBy, array $data): ElectionNomination;

    public function findNominationOrFail(string $uuid): ElectionNomination;

    public function updateNominationStatus(ElectionNomination $nomination, NominationStatus $status): ElectionNomination;

    public function castVote(BoardElection $election, ElectionNomination $nomination, User $voter): ElectionVote;

    public function tally(BoardElection $election): array;
}
