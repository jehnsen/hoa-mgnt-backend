<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ViolationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AppendEvidenceRequest;
use App\Http\Requests\Api\CreateViolationRequest;
use App\Http\Requests\Api\UpdateViolationStatusRequest;
use App\Http\Resources\ViolationResource;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Slim controller: no business logic, no direct Eloquent access.
 * All domain work is delegated to ViolationServiceInterface.
 */
final class ViolationController extends Controller
{
    public function __construct(
        private readonly ViolationServiceInterface $violationService,
        private readonly PropertyServiceInterface  $propertyService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ViolationResource::collection(
            $this->violationService->list(
                status:     request()->enum('status', ViolationStatus::class),
                propertyId: request()->integer('property_id') ?: null,
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new ViolationResource($this->violationService->findOrFail($uuid))
        );
    }

    public function store(CreateViolationRequest $request): JsonResponse
    {
        // Resolve the property UUID from the request body to its integer FK
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());

        $violation = $this->violationService->create(
            data: array_merge(
                $request->safe()->except('evidence_images', 'property_id'),
                ['property_id' => $property->id]
            ),
            reporter:       $request->user(),
            evidenceImages: $request->input('evidence_images', []),
        );

        return $this->successResponse(
            new ViolationResource($violation->load(['property', 'reporter'])),
            'Violation report logged successfully.',
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(UpdateViolationStatusRequest $request, string $uuid): JsonResponse
    {
        $newStatus = ViolationStatus::from($request->string('status')->toString());

        return $this->successResponse(
            new ViolationResource($this->violationService->updateStatus($uuid, $newStatus)),
            'Violation status updated.'
        );
    }

    public function appendEvidence(AppendEvidenceRequest $request, string $uuid): JsonResponse
    {
        return $this->successResponse(
            new ViolationResource(
                $this->violationService->appendEvidence($uuid, $request->validated('evidence_images'))
            ),
            'Evidence images appended.'
        );
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
