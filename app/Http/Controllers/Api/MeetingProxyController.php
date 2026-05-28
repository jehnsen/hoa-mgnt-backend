<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMeetingProxyRequest;
use App\Http\Resources\MeetingProxyResource;
use App\Services\Contracts\MeetingProxyServiceInterface;
use App\Services\Contracts\MeetingServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class MeetingProxyController extends Controller
{
    public function __construct(
        private readonly MeetingProxyServiceInterface $proxyService,
        private readonly MeetingServiceInterface      $meetingService,
        private readonly UserServiceInterface         $userService,
    ) {}

    public function index(string $meetingUuid): AnonymousResourceCollection
    {
        $meeting = $this->meetingService->findOrFail($meetingUuid);

        return MeetingProxyResource::collection($this->proxyService->forMeeting($meeting));
    }

    public function store(StoreMeetingProxyRequest $request, string $meetingUuid): JsonResponse
    {
        $meeting = $this->meetingService->findOrFail($meetingUuid);
        $proxy   = $this->userService->findOrFail($request->string('proxy_user_uuid')->toString());

        $meetingProxy = $this->proxyService->grant($meeting, $request->user(), $proxy);

        return $this->successResponse(
            new MeetingProxyResource($meetingProxy->load(['grantor', 'proxy'])),
            'Proxy granted successfully.',
            Response::HTTP_CREATED
        );
    }

    public function revoke(string $meetingUuid, string $proxyUuid): JsonResponse
    {
        $proxy   = $this->proxyService->findOrFail($proxyUuid);
        $revoked = $this->proxyService->revoke($proxy, request()->user());

        return $this->successResponse(new MeetingProxyResource($revoked->load(['grantor', 'proxy'])), 'Proxy revoked.');
    }

}
