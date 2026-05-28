<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVehicleRequest;
use App\Http\Requests\Api\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Services\Contracts\VehicleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleServiceInterface $vehicleService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return VehicleResource::collection(
            $this->vehicleService->list(request()->user(), $this->perPage())
        );
    }

    public function indexForProperty(string $propertyUuid): AnonymousResourceCollection
    {
        return VehicleResource::collection(
            $this->vehicleService->listForProperty($propertyUuid, request()->user(), $this->perPage())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(new VehicleResource(
            $this->vehicleService->findOrFail($uuid)
        ));
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->create($request->validated(), $request->user());

        return $this->successResponse(
            new VehicleResource($vehicle->load(['property', 'registrant'])),
            'Vehicle registered successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateVehicleRequest $request, string $uuid): JsonResponse
    {
        $vehicle = $this->vehicleService->findOrFail($uuid);
        $updated = $this->vehicleService->update($vehicle, $request->validated(), $request->user());

        return $this->successResponse(new VehicleResource($updated->load(['property', 'registrant'])), 'Vehicle updated.');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->vehicleService->delete($this->vehicleService->findOrFail($uuid));

        return $this->successResponse(null, 'Vehicle removed.');
    }

}
