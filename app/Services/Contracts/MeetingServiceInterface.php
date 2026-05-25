<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\MeetingStatus;
use App\Enums\VoteStatus;
use App\Models\Meeting;
use App\Models\MeetingVote;
use App\Models\User;
use App\Models\VoteResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MeetingServiceInterface
{
    /** @return LengthAwarePaginator<Meeting> */
    public function list(?MeetingStatus $status, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Meeting;

    /** @param array<string, mixed> $data */
    public function create(array $data, User $organizer): Meeting;

    /** @param array<string, mixed> $data */
    public function update(string $uuid, array $data): Meeting;

    public function updateStatus(string $uuid, MeetingStatus $newStatus): Meeting;

    /** @param array<string, mixed> $data */
    public function createVote(string $meetingUuid, array $data, User $creator): MeetingVote;

    public function closeVote(string $voteUuid): MeetingVote;

    public function castVote(string $voteUuid, string $selectedOption, User $voter): VoteResponse;

    /** @return array<string, int> */
    public function tallyVote(string $voteUuid): array;
}
