<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Meeting;
use App\Models\MeetingProxy;
use App\Repositories\Contracts\MeetingProxyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MeetingProxyRepository implements MeetingProxyRepositoryInterface
{
    public function __construct(private readonly MeetingProxy $model) {}

    public function findByUuid(string $uuid): ?MeetingProxy
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['meeting', 'grantor', 'proxy'])
                           ->first();
    }

    public function forMeeting(Meeting $meeting): Collection
    {
        return $this->model->newQuery()
                           ->where('meeting_id', $meeting->id)
                           ->with(['grantor', 'proxy'])
                           ->get();
    }

    public function findActiveProxy(int $meetingId, int $grantorId, int $proxyId): ?MeetingProxy
    {
        return $this->model->newQuery()
                           ->where('meeting_id', $meetingId)
                           ->where('grantor_id', $grantorId)
                           ->where('proxy_id', $proxyId)
                           ->where('is_active', true)
                           ->first();
    }

    public function findActiveForGrantor(int $meetingId, int $grantorId): ?MeetingProxy
    {
        return $this->model->newQuery()
                           ->where('meeting_id', $meetingId)
                           ->where('grantor_id', $grantorId)
                           ->where('is_active', true)
                           ->first();
    }

    public function create(array $data): MeetingProxy
    {
        return $this->model->newQuery()->create($data);
    }

    public function revoke(MeetingProxy $proxy): MeetingProxy
    {
        $proxy->is_active = false;
        $proxy->save();

        return $proxy->refresh();
    }
}
