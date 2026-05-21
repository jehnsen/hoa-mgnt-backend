<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignResidentRequest;
use App\Http\Requests\Api\StorePropertyRequest;
use App\Http\Requests\Api\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Services\Contracts\PropertyServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Slim controller — all domain logic delegated to PropertyServiceInterface.
 */
final class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyServiceInterface $propertyService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return PropertyResource::collection($this->propertyService->list());
    }

    public function show(string $property): JsonResponse
    {
        return $this->successResponse(
            new PropertyResource($this->propertyService->findOrFail($property))
        );
    }

    public function store(StorePropertyRequest $request): JsonResponse
    {
        $property = $this->propertyService->create($request->validated());

        return $this->successResponse(
            new PropertyResource($property),
            'Property created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdatePropertyRequest $request, string $property): JsonResponse
    {
        return $this->successResponse(
            new PropertyResource($this->propertyService->update($property, $request->validated())),
            'Property updated successfully.'
        );
    }

    public function assignResident(AssignResidentRequest $request, string $property): JsonResponse
    {
        $pivotData = $request->safe()->except('user_uuid');

        $this->propertyService->assignResident(
            $property,
            $request->string('user_uuid')->toString(),
            $pivotData
        );

        return $this->successResponse(
            new PropertyResource($this->propertyService->findOrFail($property)->load('residents')),
            'Resident assigned successfully.'
        );
    }

    public function unassignResident(string $property, string $user): JsonResponse
    {
        $this->propertyService->unassignResident($property, $user);

        return $this->successResponse(null, 'Resident unassigned successfully.');
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
