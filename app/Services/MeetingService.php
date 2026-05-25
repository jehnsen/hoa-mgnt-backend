<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MeetingStatus;
use App\Enums\VoteStatus;
use App\Models\Meeting;
use App\Models\MeetingVote;
use App\Models\User;
use App\Models\VoteResponse;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\MeetingVoteRepositoryInterface;
use App\Services\Contracts\MeetingServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MeetingService implements MeetingServiceInterface
{
    public function __construct(
        private readonly MeetingRepositoryInterface     $meetingRepository,
        private readonly MeetingVoteRepositoryInterface $voteRepository,
    ) {}

    public function list(?MeetingStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        return $this->meetingRepository->paginateFiltered($status, $perPage);
    }

    public function findOrFail(string $uuid): Meeting
    {
        $meeting = $this->meetingRepository->findByUuid($uuid);

        if ($meeting === null) {
            throw new NotFoundHttpException("Meeting [{$uuid}] not found.");
        }

        return $meeting;
    }

    public function create(array $data, User $organizer): Meeting
    {
        return $this->meetingRepository->create(
            array_merge($data, [
                'created_by' => $organizer->id,
                'status'     => MeetingStatus::Scheduled,
            ])
        );
    }

    public function update(string $uuid, array $data): Meeting
    {
        $meeting = $this->findOrFail($uuid);

        return $this->meetingRepository->update($meeting, $data);
    }

    public function updateStatus(string $uuid, MeetingStatus $newStatus): Meeting
    {
        $meeting = $this->findOrFail($uuid);

        if (! $meeting->canTransitionTo($newStatus)) {
            throw new HttpException(422, "Cannot transition meeting from [{$meeting->status->value}] to [{$newStatus->value}].");
        }

        return $this->meetingRepository->update($meeting, ['status' => $newStatus]);
    }

    public function createVote(string $meetingUuid, array $data, User $creator): MeetingVote
    {
        $meeting = $this->findOrFail($meetingUuid);

        return DB::transaction(function () use ($meeting, $data, $creator): MeetingVote {
            return $this->voteRepository->createVote([
                'meeting_id'   => $meeting->id,
                'created_by'   => $creator->id,
                'question'     => $data['question'],
                'options'      => $data['options'],
                'closes_at'    => $data['closes_at'] ?? null,
                'is_anonymous' => $data['is_anonymous'] ?? false,
                'status'       => VoteStatus::Open,
            ]);
        });
    }

    public function closeVote(string $voteUuid): MeetingVote
    {
        $vote = $this->findVoteOrFail($voteUuid);

        return $this->voteRepository->updateVote($vote, ['status' => VoteStatus::Closed]);
    }

    public function castVote(string $voteUuid, string $selectedOption, User $voter): VoteResponse
    {
        $vote = $this->findVoteOrFail($voteUuid);

        if (! $vote->isOpen()) {
            throw new HttpException(422, 'This vote is closed.');
        }

        if (! in_array($selectedOption, $vote->options, true)) {
            throw new HttpException(422, 'Selected option is not valid for this vote.');
        }

        if ($this->voteRepository->findResponseByUserAndVote($voter->id, $vote->id) !== null) {
            throw new HttpException(409, 'You have already cast your vote.');
        }

        return DB::transaction(function () use ($vote, $selectedOption, $voter): VoteResponse {
            return $this->voteRepository->castResponse([
                'vote_id'         => $vote->id,
                'user_id'         => $voter->id,
                'selected_option' => $selectedOption,
                'voted_at'        => now(),
            ]);
        });
    }

    public function tallyVote(string $voteUuid): array
    {
        $vote = $this->findVoteOrFail($voteUuid);

        return $this->voteRepository->tally($vote);
    }

    private function findVoteOrFail(string $uuid): MeetingVote
    {
        $vote = $this->voteRepository->findByUuid($uuid);

        if ($vote === null) {
            throw new NotFoundHttpException("Vote [{$uuid}] not found.");
        }

        return $vote;
    }
}
