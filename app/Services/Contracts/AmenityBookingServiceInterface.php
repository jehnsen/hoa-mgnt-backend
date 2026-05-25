<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\BookingStatus;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AmenityBookingServiceInterface
{
    /** @return LengthAwarePaginator<Amenity> */
    public function listAmenities(bool $activeOnly, int $perPage = 20): LengthAwarePaginator;

    public function findAmenityOrFail(string $uuid): Amenity;

    /** @param array<string, mixed> $data */
    public function createAmenity(array $data): Amenity;

    /** @param array<string, mixed> $data */
    public function updateAmenity(string $uuid, array $data): Amenity;

    /** @return LengthAwarePaginator<AmenityBooking> */
    public function listBookings(?string $amenityUuid, ?int $propertyId, ?BookingStatus $status, int $perPage = 20): LengthAwarePaginator;

    public function findBookingOrFail(string $uuid): AmenityBooking;

    /** @param array<string, mixed> $data */
    public function book(array $data, User $booker): AmenityBooking;

    public function updateBookingStatus(string $uuid, BookingStatus $status, ?string $cancellationReason = null): AmenityBooking;
}
