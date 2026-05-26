<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CastVoteRequest;
use App\Http\Resources\MeetingVoteResource;
use App\Services\Contracts\MeetingServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class VoteController extends Controller
{
    public function __construct(
        private readonly MeetingServiceInterface $meetingService,
        private readonly UserServiceInterface    $userService,
    ) {}

    public function cast(CastVoteRequest $request, string $uuid): JsonResponse
    {
        $onBehalfOf = null;
        if ($request->filled('on_behalf_of_uuid')) {
            $onBehalfOf = $this->userService->findOrFail($request->string('on_behalf_of_uuid')->toString());
        }

        $response = $this->meetingService->castVote(
            $uuid,
            $request->string('selected_option')->toString(),
            $request->user(),
            $onBehalfOf,
        );

        return $this->successResponse(
            ['voted_at' => $response->voted_at->toIso8601String()],
            'Vote cast successfully.',
            Response::HTTP_CREATED
        );
    }

    public function close(string $uuid): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        $vote = $this->meetingService->closeVote($uuid);

        return $this->successResponse(
            new MeetingVoteResource($vote),
            'Vote closed.'
        );
    }

    public function tally(string $uuid): JsonResponse
    {
        return $this->successResponse(
            ['tally' => $this->meetingService->tallyVote($uuid)]
        );
    }

    private function ensureBoardOrAdmin(): void
    {
        $user = request()->user();

        if (! ($user?->isSuperAdmin() || $user?->isBoardMember())) {
            abort(Response::HTTP_FORBIDDEN, 'Only board members or admins can perform this action.');
        }
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
