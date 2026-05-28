<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Notifications\MaintenanceRequestSubmittedNotification;
use App\Repositories\Contracts\MaintenanceRequestRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\MaintenanceServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MaintenanceService implements MaintenanceServiceInterface
{
    public function __construct(
        private readonly MaintenanceRequestRepositoryInterface $maintenanceRepository,
        private readonly AuditLogger                          $auditLogger,
    ) {}

    public function list(?int $propertyId, ?MaintenanceStatus $status, ?User $requester, int $perPage = 20): LengthAwarePaginator
    {
        return $this->maintenanceRepository->paginateFiltered(
            $propertyId,
            $status,
            $requester?->id,
            $perPage,
        );
    }

    public function findOrFail(string $uuid): MaintenanceRequest
    {
        $request = $this->maintenanceRepository->findByUuid($uuid);

        if ($request === null) {
            throw new NotFoundHttpException("Maintenance request [{$uuid}] not found.");
        }

        return $request;
    }

    public function create(array $data, User $submitter): MaintenanceRequest
    {
        $request = $this->maintenanceRepository->create(
            array_merge($data, [
                'submitted_by' => $submitter->id,
                'status'       => MaintenanceStatus::Submitted,
            ])
        );

        $submitter->notify(new MaintenanceRequestSubmittedNotification($request->load('property')));

        return $request;
    }

    public function updateStatus(string $uuid, MaintenanceStatus $newStatus, ?string $resolutionNotes = null, ?int $assignedTo = null, ?float $actualCost = null): MaintenanceRequest
    {
        $request        = $this->findOrFail($uuid);
        $previousStatus = $request->status;

        if (! $request->canTransitionTo($newStatus)) {
            throw new HttpException(422, "Cannot transition from [{$request->status->value}] to [{$newStatus->value}].");
        }

        $updates = ['status' => $newStatus];

        if ($resolutionNotes !== null) {
            $updates['resolution_notes'] = $resolutionNotes;
        }

        if ($assignedTo !== null) {
            $updates['assigned_to'] = $assignedTo;
        }

        if ($actualCost !== null) {
            $updates['actual_cost'] = $actualCost;
        }

        if ($newStatus === MaintenanceStatus::Resolved) {
            $updates['resolved_at'] = now();
        }

        $updated = $this->maintenanceRepository->update($request, $updates);

        $this->auditLogger->log(
            'maintenance_request',
            $updated->uuid,
            'status_updated',
            ['status' => $previousStatus->value],
            ['status' => $newStatus->value],
        );

        return $updated;
    }

    /** @param UploadedFile[] $files */
    public function appendPhotos(MaintenanceRequest $request, array $files): MaintenanceRequest
    {
        $photos = $request->photos ?? [];

        foreach ($files as $file) {
            $photos[] = Storage::disk('local')->putFile("maintenance/{$request->uuid}", $file);
        }

        return $this->maintenanceRepository->update($request, ['photos' => $photos]);
    }
}
