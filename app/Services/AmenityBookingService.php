<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\InvoiceType;
use App\Models\Amenity;
use App\Models\AmenityBooking;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Repositories\Contracts\AmenityBlackoutRepositoryInterface;
use App\Repositories\Contracts\AmenityBookingRepositoryInterface;
use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\AmenityBookingServiceInterface;
use App\Services\Contracts\BillingServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AmenityBookingService implements AmenityBookingServiceInterface
{
    public function __construct(
        private readonly AmenityRepositoryInterface         $amenityRepository,
        private readonly AmenityBookingRepositoryInterface  $bookingRepository,
        private readonly AmenityBlackoutRepositoryInterface $blackoutRepository,
        private readonly PropertyRepositoryInterface        $propertyRepository,
        private readonly BillingServiceInterface            $billingService,
        private readonly AuditLogger                        $auditLogger,
    ) {}

    public function listAmenities(bool $activeOnly, int $perPage = 20): LengthAwarePaginator
    {
        return $this->amenityRepository->paginate($activeOnly, $perPage);
    }

    public function findAmenityOrFail(string $uuid): Amenity
    {
        $amenity = $this->amenityRepository->findByUuid($uuid);

        if ($amenity === null) {
            throw new NotFoundHttpException("Amenity [{$uuid}] not found.");
        }

        return $amenity;
    }

    public function createAmenity(array $data): Amenity
    {
        return $this->amenityRepository->create($data);
    }

    public function updateAmenity(string $uuid, array $data): Amenity
    {
        $amenity = $this->findAmenityOrFail($uuid);

        return $this->amenityRepository->update($amenity, $data);
    }

    public function listBookings(?string $amenityUuid, ?int $propertyId, ?BookingStatus $status, int $perPage = 20): LengthAwarePaginator
    {
        $amenityId = null;

        if ($amenityUuid !== null) {
            $amenity   = $this->findAmenityOrFail($amenityUuid);
            $amenityId = $amenity->id;
        }

        return $this->bookingRepository->paginateFiltered($amenityId, $propertyId, $status, $perPage);
    }

    public function findBookingOrFail(string $uuid): AmenityBooking
    {
        $booking = $this->bookingRepository->findByUuid($uuid);

        if ($booking === null) {
            throw new NotFoundHttpException("Booking [{$uuid}] not found.");
        }

        return $booking;
    }

    public function book(array $data, User $booker): AmenityBooking
    {
        $amenity = $this->findAmenityOrFail($data['amenity_id']);

        if (! $amenity->is_active) {
            throw new HttpException(422, 'This amenity is not available for booking.');
        }

        if ($this->bookingRepository->hasConflict($amenity->id, $data['start_at'], $data['end_at'])) {
            throw new HttpException(409, 'The requested time slot conflicts with an existing booking.');
        }

        if ($this->blackoutRepository->hasConflict($amenity->id, $data['start_at'], $data['end_at'])) {
            throw new HttpException(409, 'The requested time slot falls within a maintenance blackout window.');
        }

        if ($amenity->monthly_booking_limit !== null) {
            $yearMonth = Carbon::parse($data['start_at'])->format('Y-m');
            $count     = $this->bookingRepository->countForPropertyInMonth($amenity->id, $data['property_id'], $yearMonth);

            if ($count >= $amenity->monthly_booking_limit) {
                throw new HttpException(422, "Monthly booking limit of {$amenity->monthly_booking_limit} for this amenity has been reached.");
            }
        }

        $booking = DB::transaction(function () use ($data, $amenity, $booker): AmenityBooking {
            $invoiceId = null;

            $feePerHour = (float) ($amenity->fee_per_hour ?? 0);
            $deposit    = (float) ($amenity->security_deposit ?? 0);

            if ($feePerHour > 0 || $deposit > 0) {
                $hours    = Carbon::parse($data['start_at'])->diffInMinutes(Carbon::parse($data['end_at'])) / 60;
                $total    = round($hours * $feePerHour + $deposit, 2);
                $property = $this->propertyRepository->findById($data['property_id']);

                $invoice   = $this->billingService->generateCustomInvoice($property, InvoiceType::AmenityBookingFee, [
                    'base_amount' => $total,
                    'description' => "Booking fee for {$amenity->name}",
                    'due_at'      => Carbon::parse($data['start_at'])->toDateString(),
                ]);
                $invoiceId = $invoice->id;
            }

            return $this->bookingRepository->create([
                'amenity_id'  => $amenity->id,
                'property_id' => $data['property_id'],
                'booked_by'   => $booker->id,
                'title'       => $data['title'],
                'start_at'    => $data['start_at'],
                'end_at'      => $data['end_at'],
                'status'      => BookingStatus::Pending,
                'notes'       => $data['notes'] ?? null,
                'invoice_id'  => $invoiceId,
            ]);
        });

        $booker->notify(new BookingConfirmedNotification($booking->load(['amenity', 'property', 'invoice'])));

        return $booking;
    }

    public function updateBookingStatus(string $uuid, BookingStatus $status, ?string $cancellationReason = null): AmenityBooking
    {
        $booking = $this->findBookingOrFail($uuid);

        $updates = ['status' => $status];

        if ($status === BookingStatus::Cancelled) {
            $updates['cancelled_at']        = now();
            $updates['cancellation_reason'] = $cancellationReason;
        }

        $updated = $this->bookingRepository->update($booking, $updates);

        $this->auditLogger->log(
            'amenity_booking',
            $updated->uuid,
            'booking_status_changed',
            ['status' => $booking->status->value],
            ['status' => $status->value],
        );

        return $updated;
    }
}
