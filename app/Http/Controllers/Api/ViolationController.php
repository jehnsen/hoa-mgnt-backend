<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ViolationCategory;
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
                category:   request()->enum('category', ViolationCategory::class),
                perPage:    $this->perPage(),
            )
        );
    }

    public function repeatOffenders(): JsonResponse
    {
        $properties = $this->violationService->repeatOffenders(
            minCount: max(1, request()->integer('min_count', 3)),
            months:   max(1, request()->integer('months', 6)),
            category: request()->enum('category', ViolationCategory::class),
        );

        return $this->successResponse($properties->map(fn ($p) => [
            'property_id'     => $p->uuid,
            'unit_number'     => $p->unit_number,
            'address'         => $p->address,
            'violation_count' => $p->violation_count,
        ])->values());
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
                ['property_id' => $property->id, 'category' => $request->input('category')]
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

}
