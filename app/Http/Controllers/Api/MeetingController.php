<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\MeetingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateMeetingRequest;
use App\Http\Requests\Api\CreateVoteRequest;
use App\Http\Requests\Api\UpdateMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\MeetingVoteResource;
use App\Services\Contracts\MeetingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class MeetingController extends Controller
{
    public function __construct(
        private readonly MeetingServiceInterface $meetingService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return MeetingResource::collection(
            $this->meetingService->list(
                request()->enum('status', MeetingStatus::class),
                $this->perPage()
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new MeetingResource($this->meetingService->findOrFail($uuid))
        );
    }

    public function store(CreateMeetingRequest $request): JsonResponse
    {
        $meeting = $this->meetingService->create($request->safe()->all(), $request->user());

        return $this->successResponse(
            new MeetingResource($meeting->load('organizer')),
            'Meeting scheduled successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateMeetingRequest $request, string $uuid): JsonResponse
    {
        $data = $request->safe()->all();

        if (isset($data['status'])) {
            $newStatus = MeetingStatus::from($data['status']);
            unset($data['status']);
            $this->meetingService->updateStatus($uuid, $newStatus);
        }

        $meeting = empty($data)
            ? $this->meetingService->findOrFail($uuid)
            : $this->meetingService->update($uuid, $data);

        return $this->successResponse(new MeetingResource($meeting), 'Meeting updated.');
    }

    public function storeVote(CreateVoteRequest $request, string $uuid): JsonResponse
    {
        $vote = $this->meetingService->createVote($uuid, $request->safe()->all(), $request->user());

        return $this->successResponse(
            new MeetingVoteResource($vote->load('creator')),
            'Vote created successfully.',
            Response::HTTP_CREATED
        );
    }

}
