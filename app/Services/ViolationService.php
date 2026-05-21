<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ViolationStatus;
use App\Events\ViolationStatusUpdated;
use App\Exceptions\InvalidViolationTransitionException;
use App\Models\User;
use App\Models\Violation;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Services\Contracts\ViolationServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ViolationService implements ViolationServiceInterface
{
    public function __construct(
        private readonly ViolationRepositoryInterface $violationRepository,
    ) {}

    public function list(?ViolationStatus $status = null, ?int $propertyId = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->violationRepository->paginateFiltered($status, $propertyId, $perPage);
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
                'property_id'    => $data['property_id'],
                'reported_by'    => $reporter->id,
                'title'          => $data['title'],
                'description'    => $data['description'],
                'status'         => ViolationStatus::Draft,
                'fine_amount'    => $data['fine_amount'] ?? 0.00,
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
            return $this->violationRepository->updateStatus($violation, $newStatus);
        });

        // Fire event outside the transaction so listeners run after the commit
        ViolationStatusUpdated::dispatch($updated, $previousStatus, $newStatus);

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
