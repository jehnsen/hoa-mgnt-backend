<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UtilityType;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;
use App\Models\UtilityMeterReading;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\UtilityMeterReadingRepositoryInterface;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\UtilityBillingServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UtilityBillingService implements UtilityBillingServiceInterface
{
    public function __construct(
        private readonly UtilityMeterReadingRepositoryInterface $readingRepository,
        private readonly PropertyRepositoryInterface            $propertyRepository,
        private readonly BillingServiceInterface                $billingService,
    ) {}

    public function list(?string $propertyUuid, ?string $utilityType, int $perPage = 20): LengthAwarePaginator
    {
        $propertyId = null;
        if ($propertyUuid !== null) {
            $property   = $this->propertyRepository->findByUuid($propertyUuid);
            $propertyId = $property?->id;
        }

        $type = $utilityType !== null ? UtilityType::from($utilityType) : null;

        return $this->readingRepository->paginateFiltered($propertyId, $type, $perPage);
    }

    public function findOrFail(string $uuid): UtilityMeterReading
    {
        $reading = $this->readingRepository->findByUuid($uuid);

        if ($reading === null) {
            throw new NotFoundHttpException("Utility reading [{$uuid}] not found.");
        }

        return $reading;
    }

    public function recordReading(Property $property, array $data, User $reader): UtilityMeterReading
    {
        $type = UtilityType::from($data['utility_type']);

        $previousReading = (float) ($data['previous_reading']
            ?? ($this->readingRepository->latestForProperty($property->id, $type)?->current_reading ?? 0));

        $currentReading = (float) $data['current_reading'];
        $consumption    = $currentReading - $previousReading;

        return $this->readingRepository->create([
            'property_id'      => $property->id,
            'utility_type'     => $type,
            'meter_number'     => $data['meter_number'] ?? null,
            'previous_reading' => $previousReading,
            'current_reading'  => $currentReading,
            'consumption'      => $consumption,
            'reading_date'     => $data['reading_date'],
            'rate_per_unit'    => $data['rate_per_unit'],
            'fixed_charge'     => $data['fixed_charge'] ?? 0,
            'notes'            => $data['notes'] ?? null,
            'read_by'          => $reader->id,
        ]);
    }

    public function generateBill(UtilityMeterReading $reading): Invoice
    {
        if ($reading->invoice_id !== null) {
            throw new HttpException(409, 'A bill has already been generated for this reading.');
        }

        return DB::transaction(function () use ($reading): Invoice {
            $property = $reading->property;
            $total    = $reading->totalAmount();
            $type     = $reading->utility_type->invoiceType();

            $invoice = $this->billingService->generateCustomInvoice($property, $type, [
                'base_amount' => $total,
                'description' => "{$reading->utility_type->label()} bill – {$reading->reading_date->format('M Y')} ({$reading->consumption} units @ {$reading->rate_per_unit})",
                'due_at'      => now()->addDays(15)->toDateString(),
            ]);

            $this->readingRepository->update($reading, ['invoice_id' => $invoice->id]);

            return $invoice;
        });
    }
}
