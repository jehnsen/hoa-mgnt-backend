<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceType;
use App\Enums\ViolationCategory;
use App\Enums\ViolationStatus;
use App\Events\ViolationStatusUpdated;
use App\Exceptions\InvalidViolationTransitionException;
use App\Models\User;
use App\Models\Violation;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\BillingServiceInterface;
use App\Services\Contracts\ViolationServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ViolationService implements ViolationServiceInterface
{
    public function __construct(
        private readonly ViolationRepositoryInterface $violationRepository,
        private readonly BillingServiceInterface      $billingService,
        private readonly AuditLogger                  $auditLogger,
    ) {}

    public function list(?ViolationStatus $status = null, ?int $propertyId = null, ?ViolationCategory $category = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->violationRepository->paginateFiltered($status, $propertyId, $category, $perPage);
    }

    public function repeatOffenders(int $minCount = 3, int $months = 6, ?ViolationCategory $category = null): Collection
    {
        return $this->violationRepository->repeatOffenders($minCount, Carbon::now()->subMonths($months), $category);
    }

    public function findOrFail(string $uuid): Violation
    {
        $violation = $this->violationRepository->findByUuid($uuid);

        if ($violation === null) {
            throw new NotFoundHttpException("Violation [{$uuid}] not found.");
        }

        return $violation;
    }

    /**
     * Create and persist a new violation report.
     *
     * Evidence images are stored as a JSON array of {path, lat, lng, captured_at}
     * objects so geotagging metadata travels with the record rather than being
     * split across a separate table (avoids joins for the common read path).
     */
    public function create(array $data, User $reporter, array $evidenceImages = []): Violation
    {
        return DB::transaction(function () use ($data, $reporter, $evidenceImages): Violation {
            return $this->violationRepository->create([
                'property_id'     => $data['property_id'],
                'reported_by'     => $reporter->id,
                'title'           => $data['title'],
                'description'     => $data['description'],
                'category'        => $data['category'] ?? null,
                'status'          => ViolationStatus::Draft,
                'fine_amount'     => $data['fine_amount'] ?? 0.00,
                'evidence_images' => empty($evidenceImages) ? null : $evidenceImages,
            ]);
        });
    }

    /**
     * Enforce the state machine and fire an application event so downstream
     * listeners (notifications, audit log, penalty invoice generation) can
     * react without coupling to this service.
     */
    public function updateStatus(string $uuid, ViolationStatus $newStatus): Violation
    {
        $violation      = $this->findOrFail($uuid);
        $previousStatus = $violation->status;

        if (! $violation->canTransitionTo($newStatus)) {
            throw new InvalidViolationTransitionException($previousStatus, $newStatus);
        }

        $updated = DB::transaction(function () use ($violation, $newStatus): Violation {
            $updated = $this->violationRepository->updateStatus($violation, $newStatus);

            // Auto-generate a ViolationFine invoice when a fine is issued for the first time
            if ($newStatus === ViolationStatus::Issued
                && $updated->invoice_id === null
                && (float) $violation->fine_amount > 0.0
            ) {
                $invoice = $this->billingService->generateCustomInvoice(
                    $violation->property,
                    InvoiceType::ViolationFine,
                    [
                        'base_amount' => $violation->fine_amount,
                        'description' => "Violation Fine – {$violation->title}",
                        'due_at'      => Carbon::now()->addDays(30)->toDateString(),
                    ]
                );

                $this->violationRepository->update($updated, ['invoice_id' => $invoice->id]);
                $updated->invoice_id = $invoice->id;
            }

            return $updated;
        });

        // Fire event outside the transaction so listeners run after the commit
        ViolationStatusUpdated::dispatch($updated, $previousStatus, $newStatus);

        $this->auditLogger->log(
            'violation',
            $updated->uuid,
            'status_updated',
            ['status' => $previousStatus->value],
            ['status' => $newStatus->value],
        );

        return $updated;
    }

    /**
     * Merge new geotagged images into the violation's existing evidence set.
     *
     * @param array<int, array{path:string, lat:float, lng:float, captured_at:string}> $images
     */
    public function appendEvidence(string $uuid, array $images): Violation
    {
        $violation       = $this->findOrFail($uuid);
        $existingImages  = $violation->evidence_images ?? [];
        $mergedImages    = array_merge($existingImages, $images);

        return DB::transaction(function () use ($violation, $mergedImages): Violation {
            return $this->violationRepository->update($violation, [
                'evidence_images' => $mergedImages,
            ]);
        });
    }
}
