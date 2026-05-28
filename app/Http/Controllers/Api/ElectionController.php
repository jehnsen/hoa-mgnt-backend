<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\NominationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CastElectionVoteRequest;
use App\Http\Requests\Api\StoreElectionRequest;
use App\Http\Requests\Api\StoreNominationRequest;
use App\Http\Requests\Api\TransitionElectionRequest;
use App\Http\Requests\Api\UpdateElectionRequest;
use App\Http\Resources\ElectionNominationResource;
use App\Http\Resources\ElectionResource;
use App\Services\Contracts\ElectionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ElectionController extends Controller
{
    public function __construct(
        private readonly ElectionServiceInterface $electionService,
        private readonly UserServiceInterface     $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ElectionResource::collection($this->electionService->list($this->perPage()));
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new ElectionResource($this->electionService->findOrFail($uuid))
        );
    }

    public function store(StoreElectionRequest $request): JsonResponse
    {
        $election = $this->electionService->create($request->safe()->all(), $request->user());

        return $this->successResponse(
            new ElectionResource($election),
            'Election created.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateElectionRequest $request, string $uuid): JsonResponse
    {
        $election = $this->electionService->findOrFail($uuid);

        return $this->successResponse(
            new ElectionResource($this->electionService->update($election, $request->safe()->all())),
            'Election updated.'
        );
    }

    public function transition(TransitionElectionRequest $request, string $uuid): JsonResponse
    {
        $election  = $this->electionService->findOrFail($uuid);
        $newStatus = $request->enum('status', \App\Enums\ElectionStatus::class);

        return $this->successResponse(
            new ElectionResource($this->electionService->transition($election, $newStatus)),
            'Election status updated.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->electionService->delete($this->electionService->findOrFail($uuid));

        return $this->successResponse(null, 'Election deleted.');
    }

    public function nominate(StoreNominationRequest $request, string $uuid): JsonResponse
    {
        $election  = $this->electionService->findOrFail($uuid);
        $nominee   = $this->userService->findOrFail($request->string('nominee_id')->toString());

        $nomination = $this->electionService->nominate(
            $election,
            $nominee,
            $request->user(),
            $request->safe()->except('nominee_id')
        );

        return $this->successResponse(
            new ElectionNominationResource($nomination->load(['nominee', 'nominator'])),
            'Nomination submitted.',
            Response::HTTP_CREATED
        );
    }

    public function updateNomination(string $nominationUuid): JsonResponse
    {
        $nomination = $this->electionService->findNominationOrFail($nominationUuid);
        $status     = NominationStatus::from(request()->string('status')->toString());

        return $this->successResponse(
            new ElectionNominationResource(
                $this->electionService->updateNominationStatus($nomination, $status)
            ),
            'Nomination status updated.'
        );
    }

    public function castVote(CastElectionVoteRequest $request, string $uuid): JsonResponse
    {
        $election   = $this->electionService->findOrFail($uuid);
        $nomination = $this->electionService->findNominationOrFail($request->string('nomination_id')->toString());

        $this->electionService->castVote($election, $nomination, $request->user());

        return $this->successResponse(null, 'Vote cast successfully.');
    }

    public function tally(string $uuid): JsonResponse
    {
        $election = $this->electionService->findOrFail($uuid);

        return $this->successResponse($this->electionService->tally($election), 'Election tally');
    }
}
