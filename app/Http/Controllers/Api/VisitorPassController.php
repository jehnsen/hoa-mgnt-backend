<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVisitorPassRequest;
use App\Http\Resources\VisitorPassResource;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\VisitorPassServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class VisitorPassController extends Controller
{
    public function __construct(
        private readonly VisitorPassServiceInterface $passService,
        private readonly PropertyServiceInterface   $propertyService,
    ) {}

    public function index(string $propertyUuid): AnonymousResourceCollection
    {
        $property   = $this->propertyService->findOrFail($propertyUuid);
        $activeOnly = request()->boolean('active_only', false);

        return VisitorPassResource::collection(
            $this->passService->forProperty($property, $activeOnly)
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new VisitorPassResource($this->passService->findOrFail($uuid))
        );
    }

    public function store(StoreVisitorPassRequest $request): JsonResponse
    {
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());

        $pass = $this->passService->create($property, $request->user(), $request->safe()->except('property_id'));

        return $this->successResponse(
            new VisitorPassResource($pass->load(['property', 'resident'])),
            'Visitor pass created.',
            Response::HTTP_CREATED
        );
    }

    public function checkIn(string $accessCode): JsonResponse
    {
        $pass = $this->passService->checkIn($accessCode);

        return $this->successResponse(
            new VisitorPassResource($pass->load(['property', 'resident'])),
            'Visitor checked in successfully.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->passService->delete($this->passService->findOrFail($uuid));

        return $this->successResponse(null, 'Visitor pass deleted.');
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
