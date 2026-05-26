<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Meeting;
use App\Models\MeetingProxy;
use Illuminate\Database\Eloquent\Collection;

interface MeetingProxyRepositoryInterface
{
    public function findByUuid(string $uuid): ?MeetingProxy;

    /** @return Collection<int, MeetingProxy> */
    public function forMeeting(Meeting $meeting): Collection;

    public function findActiveProxy(int $meetingId, int $grantorId, int $proxyId): ?MeetingProxy;

    public function findActiveForGrantor(int $meetingId, int $grantorId): ?MeetingProxy;

    /** @param array<string, mixed> $data */
    public function create(array $data): MeetingProxy;

    public function revoke(MeetingProxy $proxy): MeetingProxy;
}
