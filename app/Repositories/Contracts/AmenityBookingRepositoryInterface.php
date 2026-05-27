<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\BookingStatus;
use App\Models\AmenityBooking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AmenityBookingRepositoryInterface
{
    public function findByUuid(string $uuid): ?AmenityBooking;

    /** @return LengthAwarePaginator<AmenityBooking> */
    public function paginateFiltered(?int $amenityId, ?int $propertyId, ?BookingStatus $status, int $perPage = 20): LengthAwarePaginator;

    public function hasConflict(int $amenityId, string $startAt, string $endAt, ?int $excludeId = null): bool;

    public function countForPropertyInMonth(int $amenityId, int $propertyId, string $yearMonth): int;

    /** @param array<string, mixed> $data */
    public function create(array $data): AmenityBooking;

    /** @param array<string, mixed> $data */
    public function update(AmenityBooking $booking, array $data): AmenityBooking;
}
