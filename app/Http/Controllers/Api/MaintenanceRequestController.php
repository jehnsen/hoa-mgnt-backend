<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateMaintenanceRequestRequest;
use App\Http\Requests\Api\UpdateMaintenanceStatusRequest;
use App\Http\Requests\Api\UploadMaintenancePhotosRequest;
use App\Http\Resources\MaintenanceRequestResource;
use App\Services\Contracts\MaintenanceServiceInterface;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class MaintenanceRequestController extends Controller
{
    public function __construct(
        private readonly MaintenanceServiceInterface $maintenanceService,
        private readonly PropertyServiceInterface   $propertyService,
        private readonly UserServiceInterface       $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $user       = request()->user();
        $propertyId = null;

        if (request()->has('property_id')) {
            $property   = $this->propertyService->findOrFail(request()->string('property_id')->toString());
            $propertyId = $property->id;
        }

        $submitter = $user->isResident() ? $user : null;

        return MaintenanceRequestResource::collection(
            $this->maintenanceService->list(
                $propertyId,
                request()->enum('status', MaintenanceStatus::class),
                $submitter,
                $this->perPage()
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new MaintenanceRequestResource($this->maintenanceService->findOrFail($uuid))
        );
    }

    public function store(CreateMaintenanceRequestRequest $request): JsonResponse
    {
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());

        $maintenanceRequest = $this->maintenanceService->create(
            array_merge(
                $request->safe()->except('property_id'),
                ['property_id' => $property->id]
            ),
            $request->user()
        );

        return $this->successResponse(
            new MaintenanceRequestResource($maintenanceRequest->load(['property', 'submitter'])),
            'Maintenance request submitted successfully.',
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(UpdateMaintenanceStatusRequest $request, string $uuid): JsonResponse
    {
        $newStatus = MaintenanceStatus::from($request->string('status')->toString());

        $assignedToId = null;
        if ($request->has('assigned_to')) {
            $assignee     = $this->userService->findOrFail($request->string('assigned_to')->toString());
            $assignedToId = $assignee->id;
        }

        return $this->successResponse(
            new MaintenanceRequestResource(
                $this->maintenanceService->updateStatus(
                    $uuid,
                    $newStatus,
                    $request->string('resolution_notes')->toString() ?: null,
                    $assignedToId,
                    $request->has('actual_cost') ? (float) $request->input('actual_cost') : null,
                )
            ),
            'Status updated.'
        );
    }

    public function uploadPhotos(UploadMaintenancePhotosRequest $request, string $uuid): JsonResponse
    {
        $maintenanceRequest = $this->maintenanceService->findOrFail($uuid);

        return $this->successResponse(
            new MaintenanceRequestResource(
                $this->maintenanceService->appendPhotos($maintenanceRequest, $request->file('photos'))
            ),
            'Photos uploaded.'
        );
    }

}
