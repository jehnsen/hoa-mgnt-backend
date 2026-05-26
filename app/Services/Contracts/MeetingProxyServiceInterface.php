<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Meeting;
use App\Models\MeetingProxy;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface MeetingProxyServiceInterface
{
    /** @return Collection<int, MeetingProxy> */
    public function forMeeting(Meeting $meeting): Collection;

    public function findOrFail(string $uuid): MeetingProxy;

    /**
     * Grant a proxy for a meeting. Throws 409 if grantor already has an active proxy.
     */
    public function grant(Meeting $meeting, User $grantor, User $proxy): MeetingProxy;

    /**
     * Revoke a proxy. Only the grantor (or SuperAdmin) may revoke.
     */
    public function revoke(MeetingProxy $proxy, User $actor): MeetingProxy;
}
