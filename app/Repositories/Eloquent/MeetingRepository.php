<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MeetingRepository implements MeetingRepositoryInterface
{
    public function __construct(private readonly Meeting $model) {}

    public function findByUuid(string $uuid): ?Meeting
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['organizer', 'votes'])
                           ->first();
    }

    public function paginateFiltered(?MeetingStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('organizer');

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('scheduled_at')->paginate($perPage);
    }

    public function create(array $data): Meeting
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Meeting $meeting, array $data): Meeting
    {
        $meeting->fill($data)->save();

        return $meeting->refresh();
    }
}
