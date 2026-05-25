<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateBookingRequest;
use App\Http\Requests\Api\UpdateBookingStatusRequest;
use App\Http\Resources\AmenityBookingResource;
use App\Services\Contracts\AmenityBookingServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AmenityBookingController extends Controller
{
    public function __construct(
        private readonly AmenityBookingServiceInterface $amenityService,
        private readonly PropertyServiceInterface       $propertyService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return AmenityBookingResource::collection(
            $this->amenityService->listBookings(
                amenityUuid: request()->string('amenity_id')->toString() ?: null,
                propertyId:  null,
                status:      request()->enum('status', BookingStatus::class),
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new AmenityBookingResource($this->amenityService->findBookingOrFail($uuid))
        );
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());

        $booking = $this->amenityService->book(
            array_merge(
                $request->safe()->except('property_id'),
                ['property_id' => $property->id]
            ),
            $request->user()
        );

        return $this->successResponse(
            new AmenityBookingResource($booking->load(['amenity', 'property', 'booker'])),
            'Booking created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(UpdateBookingStatusRequest $request, string $uuid): JsonResponse
    {
        $newStatus = BookingStatus::from($request->string('status')->toString());

        return $this->successResponse(
            new AmenityBookingResource(
                $this->amenityService->updateBookingStatus(
                    $uuid,
                    $newStatus,
                    $request->string('cancellation_reason')->toString() ?: null,
                )
            ),
            'Booking status updated.'
        );
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
