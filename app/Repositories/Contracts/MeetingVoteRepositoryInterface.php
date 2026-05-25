<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\MeetingVote;
use App\Models\VoteResponse;
use Illuminate\Support\Collection;

interface MeetingVoteRepositoryInterface
{
    public function findByUuid(string $uuid): ?MeetingVote;

    /** @return Collection<int, MeetingVote> */
    public function forMeeting(int $meetingId): Collection;

    public function findResponseByUserAndVote(int $userId, int $voteId): ?VoteResponse;

    /** @param array<string, mixed> $data */
    public function createVote(array $data): MeetingVote;

    /** @param array<string, mixed> $data */
    public function castResponse(array $data): VoteResponse;

    /** @param array<string, mixed> $data */
    public function updateVote(MeetingVote $vote, array $data): MeetingVote;

    /** @return array<string, int> option => count */
    public function tally(MeetingVote $vote): array;
}
