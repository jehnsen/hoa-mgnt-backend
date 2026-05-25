<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\MeetingVote;
use App\Models\VoteResponse;
use App\Repositories\Contracts\MeetingVoteRepositoryInterface;
use Illuminate\Support\Collection;

class MeetingVoteRepository implements MeetingVoteRepositoryInterface
{
    public function __construct(
        private readonly MeetingVote  $voteModel,
        private readonly VoteResponse $responseModel,
    ) {}

    public function findByUuid(string $uuid): ?MeetingVote
    {
        return $this->voteModel->newQuery()
                               ->where('uuid', $uuid)
                               ->with(['meeting', 'creator', 'responses.voter'])
                               ->first();
    }

    public function forMeeting(int $meetingId): Collection
    {
        return $this->voteModel->newQuery()
                               ->where('meeting_id', $meetingId)
                               ->with(['creator', 'responses'])
                               ->get();
    }

    public function findResponseByUserAndVote(int $userId, int $voteId): ?VoteResponse
    {
        return $this->responseModel->newQuery()
                                   ->where('vote_id', $voteId)
                                   ->where('user_id', $userId)
                                   ->first();
    }

    public function createVote(array $data): MeetingVote
    {
        return $this->voteModel->newQuery()->create($data);
    }

    public function castResponse(array $data): VoteResponse
    {
        return $this->responseModel->newQuery()->create($data);
    }

    public function updateVote(MeetingVote $vote, array $data): MeetingVote
    {
        $vote->fill($data)->save();

        return $vote->refresh();
    }

    public function tally(MeetingVote $vote): array
    {
        $counts = $this->responseModel->newQuery()
                                      ->where('vote_id', $vote->id)
                                      ->selectRaw('selected_option, COUNT(*) as total')
                                      ->groupBy('selected_option')
                                      ->pluck('total', 'selected_option')
                                      ->toArray();

        // Ensure every option appears in the tally even with 0 votes
        $tally = [];
        foreach ($vote->options as $option) {
            $tally[$option] = $counts[$option] ?? 0;
        }

        return $tally;
    }
}
