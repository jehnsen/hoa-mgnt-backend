<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ElectionStatus;
use App\Enums\NominationStatus;
use App\Models\BoardElection;
use App\Models\ElectionNomination;
use App\Models\ElectionVote;
use App\Models\User;
use App\Repositories\Contracts\ElectionNominationRepositoryInterface;
use App\Repositories\Contracts\ElectionRepositoryInterface;
use App\Repositories\Contracts\ElectionVoteRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\ElectionServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ElectionService implements ElectionServiceInterface
{
    public function __construct(
        private readonly ElectionRepositoryInterface           $electionRepository,
        private readonly ElectionNominationRepositoryInterface $nominationRepository,
        private readonly ElectionVoteRepositoryInterface       $voteRepository,
        private readonly AuditLogger                           $auditLogger,
    ) {}

    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return $this->electionRepository->paginate($perPage);
    }

    public function findOrFail(string $uuid): BoardElection
    {
        $election = $this->electionRepository->findByUuid($uuid);

        if ($election === null) {
            throw new NotFoundHttpException("Election [{$uuid}] not found.");
        }

        return $election;
    }

    public function create(array $data, User $creator): BoardElection
    {
        return $this->electionRepository->create(array_merge($data, [
            'status'     => ElectionStatus::Draft,
            'created_by' => $creator->id,
        ]));
    }

    public function update(BoardElection $election, array $data): BoardElection
    {
        return $this->electionRepository->update($election, $data);
    }

    public function transition(BoardElection $election, ElectionStatus $newStatus): BoardElection
    {
        if (! $election->status->canTransitionTo($newStatus)) {
            throw new HttpException(422, "Cannot transition election from [{$election->status->value}] to [{$newStatus->value}].");
        }

        $updated = $this->electionRepository->update($election, ['status' => $newStatus]);

        $this->auditLogger->log(
            'board_election',
            $updated->uuid,
            'election_status_changed',
            ['status' => $election->status->value],
            ['status' => $newStatus->value],
        );

        return $updated;
    }

    public function delete(BoardElection $election): void
    {
        $this->electionRepository->delete($election);
    }

    public function nominate(BoardElection $election, User $nominee, User $nominatedBy, array $data): ElectionNomination
    {
        if ($election->status !== ElectionStatus::NominationsOpen) {
            throw new HttpException(422, 'Nominations are not open for this election.');
        }

        $positionValue = $data['position_value'];

        if ($this->nominationRepository->findDuplicate($election->id, $nominee->id, $positionValue) !== null) {
            throw new HttpException(409, 'This candidate has already been nominated for this position.');
        }

        return $this->nominationRepository->create([
            'election_id'         => $election->id,
            'nominee_id'          => $nominee->id,
            'nominated_by'        => $nominatedBy->id,
            'position_value'      => $positionValue,
            'candidate_statement' => $data['candidate_statement'] ?? null,
            'status'              => NominationStatus::Pending,
        ]);
    }

    public function findNominationOrFail(string $uuid): ElectionNomination
    {
        $nomination = $this->nominationRepository->findByUuid($uuid);

        if ($nomination === null) {
            throw new NotFoundHttpException("Nomination [{$uuid}] not found.");
        }

        return $nomination;
    }

    public function updateNominationStatus(ElectionNomination $nomination, NominationStatus $status): ElectionNomination
    {
        return $this->nominationRepository->update($nomination, ['status' => $status]);
    }

    public function castVote(BoardElection $election, ElectionNomination $nomination, User $voter): ElectionVote
    {
        if ($election->status !== ElectionStatus::VotingOpen) {
            throw new HttpException(422, 'Voting is not currently open for this election.');
        }

        if ($nomination->election_id !== $election->id) {
            throw new HttpException(422, 'This nomination does not belong to the specified election.');
        }

        if ($nomination->status !== NominationStatus::Accepted) {
            throw new HttpException(422, 'You can only vote for accepted nominations.');
        }

        $positionValue = $nomination->position_value->value;

        if ($this->voteRepository->hasVoted($election->id, $voter->id, $positionValue)) {
            throw new HttpException(409, "You have already voted for the {$nomination->position_value->label()} position.");
        }

        return DB::transaction(function () use ($election, $nomination, $voter, $positionValue): ElectionVote {
            return $this->voteRepository->create([
                'election_id'    => $election->id,
                'voter_id'       => $voter->id,
                'nomination_id'  => $nomination->id,
                'position_value' => $positionValue,
                'cast_at'        => now(),
            ]);
        });
    }

    public function tally(BoardElection $election): array
    {
        $votes = $this->voteRepository->tallyForElection($election->id);

        $byPosition = $votes->groupBy('position_value');

        $result = [];
        foreach ($byPosition as $position => $positionVotes) {
            $byCandidates = $positionVotes->groupBy('nomination_id')->map->count();

            $nominationDetails = $positionVotes->keyBy('nomination_id')->map(fn ($v) => [
                'nomination_id'   => $v->nomination->uuid ?? null,
                'nominee_name'    => $v->nomination->nominee->name ?? null,
                'vote_count'      => $byCandidates[$v->nomination_id] ?? 0,
            ])->unique('nomination_id')->values();

            $result[$position] = [
                'position'      => $position,
                'total_votes'   => $positionVotes->count(),
                'candidates'    => $nominationDetails->sortByDesc('vote_count')->values(),
            ];
        }

        return [
            'election_id' => $election->uuid,
            'status'      => $election->status->value,
            'results'     => array_values($result),
        ];
    }
}
