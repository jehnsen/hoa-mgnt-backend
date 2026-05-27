<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\Contracts\DelinquencyServiceInterface;
use Illuminate\Support\Carbon;

final class DelinquencyService implements DelinquencyServiceInterface
{
    private const THRESHOLD = 3;

    public function __construct(
        private readonly InvoiceRepositoryInterface  $invoiceRepository,
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    public function isDelinquent(Property $property): bool
    {
        return $this->invoiceRepository->countConsecutiveOverdueMonthlyDues($property->id) >= self::THRESHOLD;
    }

    public function runEscalation(): array
    {
        $flagged  = 0;
        $cleared  = 0;

        foreach ($this->propertyRepository->allActive() as $property) {
            $delinquent = $this->isDelinquent($property);

            if ($delinquent && ! $property->is_delinquent) {
                $this->propertyRepository->update($property, [
                    'is_delinquent'    => true,
                    'delinquent_since' => Carbon::today()->toDateString(),
                ]);
                $flagged++;
            } elseif (! $delinquent && $property->is_delinquent) {
                $this->propertyRepository->update($property, [
                    'is_delinquent'    => false,
                    'delinquent_since' => null,
                ]);
                $cleared++;
            }
        }

        return ['flagged' => $flagged, 'cleared' => $cleared];
    }
}
