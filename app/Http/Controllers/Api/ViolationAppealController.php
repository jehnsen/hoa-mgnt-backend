<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreViolationAppealRequest;
use App\Http\Resources\ViolationAppealResource;
use App\Services\Contracts\ViolationAppealServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ViolationAppealController extends Controller
{
    public function __construct(
        private readonly ViolationAppealServiceInterface $appealService,
        private readonly ViolationServiceInterface       $violationService,
    ) {}

    public function index(string $violationUuid): AnonymousResourceCollection
    {
        $violation = $this->violationService->findOrFail($violationUuid);

        return ViolationAppealResource::collection(
            $this->appealService->forViolation($violation)
        );
    }

    public function store(StoreViolationAppealRequest $request, string $violationUuid): JsonResponse
    {
        $violation = $this->violationService->findOrFail($violationUuid);
        $appeal    = $this->appealService->create($violation, $request->validated(), $request->user());

        return $this->successResponse(
            new ViolationAppealResource($appeal),
            'Appeal submitted successfully.',
            Response::HTTP_CREATED
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(new ViolationAppealResource($this->appealService->findOrFail($uuid)));
    }

}
