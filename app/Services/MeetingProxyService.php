<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Meeting;
use App\Models\MeetingProxy;
use App\Models\User;
use App\Repositories\Contracts\MeetingProxyRepositoryInterface;
use App\Services\Contracts\MeetingProxyServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MeetingProxyService implements MeetingProxyServiceInterface
{
    public function __construct(
        private readonly MeetingProxyRepositoryInterface $proxyRepository,
    ) {}

    public function forMeeting(Meeting $meeting): Collection
    {
        return $this->proxyRepository->forMeeting($meeting);
    }

    public function findOrFail(string $uuid): MeetingProxy
    {
        $proxy = $this->proxyRepository->findByUuid($uuid);

        if ($proxy === null) {
            throw new NotFoundHttpException("Meeting proxy [{$uuid}] not found.");
        }

        return $proxy;
    }

    public function grant(Meeting $meeting, User $grantor, User $proxy): MeetingProxy
    {
        if ($grantor->id === $proxy->id) {
            throw new HttpException(422, 'You cannot designate yourself as your own proxy.');
        }

        $existing = $this->proxyRepository->findActiveForGrantor($meeting->id, $grantor->id);

        if ($existing !== null) {
            throw new HttpException(409, 'You already have an active proxy for this meeting. Revoke it first.');
        }

        return DB::transaction(function () use ($meeting, $grantor, $proxy): MeetingProxy {
            return $this->proxyRepository->create([
                'meeting_id' => $meeting->id,
                'grantor_id' => $grantor->id,
                'proxy_id'   => $proxy->id,
                'is_active'  => true,
            ]);
        });
    }

    public function revoke(MeetingProxy $proxy, User $actor): MeetingProxy
    {
        if (! $actor->isSuperAdmin() && $actor->id !== $proxy->grantor_id) {
            throw new HttpException(403, 'Only the grantor or a super admin can revoke a proxy.');
        }

        if (! $proxy->is_active) {
            throw new HttpException(422, 'This proxy is already revoked.');
        }

        return $this->proxyRepository->revoke($proxy);
    }
}
