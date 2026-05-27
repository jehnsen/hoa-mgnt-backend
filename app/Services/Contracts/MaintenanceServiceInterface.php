<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface MaintenanceServiceInterface
{
    /** @return LengthAwarePaginator<MaintenanceRequest> */
    public function list(?int $propertyId, ?MaintenanceStatus $status, ?User $requester, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): MaintenanceRequest;

    /** @param array<string, mixed> $data */
    public function create(array $data, User $submitter): MaintenanceRequest;

    public function updateStatus(string $uuid, MaintenanceStatus $newStatus, ?string $resolutionNotes = null, ?int $assignedTo = null, ?float $actualCost = null): MaintenanceRequest;

    /** @param UploadedFile[] $files */
    public function appendPhotos(MaintenanceRequest $request, array $files): MaintenanceRequest;
}
