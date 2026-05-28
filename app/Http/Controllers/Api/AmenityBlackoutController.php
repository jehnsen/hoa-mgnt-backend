<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAmenityBlackoutRequest;
use App\Http\Resources\AmenityBlackoutResource;
use App\Services\Contracts\AmenityBlackoutServiceInterface;
use App\Services\Contracts\AmenityBookingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AmenityBlackoutController extends Controller
{
    public function __construct(
        private readonly AmenityBookingServiceInterface $amenityService,
        private readonly AmenityBlackoutServiceInterface $blackoutService,
    ) {}

    public function index(string $amenityUuid): AnonymousResourceCollection
    {
        $amenity = $this->amenityService->findAmenityOrFail($amenityUuid);

        return AmenityBlackoutResource::collection(
            $this->blackoutService->forAmenity($amenity)
        );
    }

    public function store(StoreAmenityBlackoutRequest $request, string $amenityUuid): JsonResponse
    {
        $amenity  = $this->amenityService->findAmenityOrFail($amenityUuid);
        $blackout = $this->blackoutService->create($amenity, $request->safe()->all());

        return $this->successResponse(
            new AmenityBlackoutResource($blackout->load('amenity')),
            'Blackout window created.',
            Response::HTTP_CREATED
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->blackoutService->delete($this->blackoutService->findOrFail($uuid));

        return $this->successResponse(null, 'Blackout window deleted.');
    }

}
