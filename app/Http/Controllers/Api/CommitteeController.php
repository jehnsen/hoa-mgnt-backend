<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCommitteeMemberRequest;
use App\Http\Requests\Api\StoreCommitteeRequest;
use App\Http\Requests\Api\UpdateCommitteeRequest;
use App\Http\Resources\CommitteeMemberResource;
use App\Http\Resources\CommitteeResource;
use App\Services\Contracts\CommitteeServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class CommitteeController extends Controller
{
    public function __construct(
        private readonly CommitteeServiceInterface $committeeService,
        private readonly UserServiceInterface      $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return CommitteeResource::collection($this->committeeService->list($this->perPage()));
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(new CommitteeResource($this->committeeService->findOrFail($uuid)));
    }

    public function store(StoreCommitteeRequest $request): JsonResponse
    {
        $committee = $this->committeeService->create($request->validated());

        return $this->successResponse(new CommitteeResource($committee), 'Committee created.', Response::HTTP_CREATED);
    }

    public function update(UpdateCommitteeRequest $request, string $uuid): JsonResponse
    {
        $committee = $this->committeeService->findOrFail($uuid);
        $updated   = $this->committeeService->update($committee, $request->validated());

        return $this->successResponse(new CommitteeResource($updated), 'Committee updated.');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->committeeService->delete($this->committeeService->findOrFail($uuid));

        return $this->successResponse(null, 'Committee deleted.');
    }

    public function addMember(AddCommitteeMemberRequest $request, string $uuid): JsonResponse
    {
        $committee = $this->committeeService->findOrFail($uuid);
        $user      = $this->userService->findOrFail($request->string('user_uuid')->toString());
        $member    = $this->committeeService->addMember($committee, $user, $request->validated());

        return $this->successResponse(
            new CommitteeMemberResource($member->load('user')),
            'Member added to committee.',
            Response::HTTP_CREATED
        );
    }

    public function removeMember(string $uuid, string $userUuid): JsonResponse
    {
        $committee = $this->committeeService->findOrFail($uuid);
        $user      = $this->userService->findOrFail($userUuid);
        $member    = $this->committeeService->removeMember($committee, $user);

        return $this->successResponse(new CommitteeMemberResource($member->load('user')), 'Member removed from committee.');
    }

}
