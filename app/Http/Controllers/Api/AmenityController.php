<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateAmenityRequest;
use App\Http\Requests\Api\UpdateAmenityRequest;
use App\Http\Resources\AmenityResource;
use App\Services\Contracts\AmenityBookingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AmenityController extends Controller
{
    public function __construct(
        private readonly AmenityBookingServiceInterface $amenityService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $activeOnly = ! (request()->user()?->isSuperAdmin() || request()->user()?->isBoardMember());

        return AmenityResource::collection(
            $this->amenityService->listAmenities($activeOnly)
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new AmenityResource($this->amenityService->findAmenityOrFail($uuid))
        );
    }

    public function store(CreateAmenityRequest $request): JsonResponse
    {
        $amenity = $this->amenityService->createAmenity($request->safe()->all());

        return $this->successResponse(
            new AmenityResource($amenity),
            'Amenity created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateAmenityRequest $request, string $uuid): JsonResponse
    {
        return $this->successResponse(
            new AmenityResource($this->amenityService->updateAmenity($uuid, $request->safe()->all())),
            'Amenity updated.'
        );
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
