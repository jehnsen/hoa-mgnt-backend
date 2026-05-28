<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignVendorRequest;
use App\Http\Requests\Api\CreateVendorProfileRequest;
use App\Http\Requests\Api\UpdateVendorProfileRequest;
use App\Http\Resources\MaintenanceRequestResource;
use App\Http\Resources\VendorProfileResource;
use App\Services\Contracts\MaintenanceServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\VendorServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class VendorController extends Controller
{
    public function __construct(
        private readonly VendorServiceInterface      $vendorService,
        private readonly UserServiceInterface        $userService,
        private readonly MaintenanceServiceInterface $maintenanceService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return VendorProfileResource::collection(
            $this->vendorService->list($this->perPage())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $profile = $this->vendorService->findOrFail($uuid);

        return $this->successResponse(new VendorProfileResource($profile));
    }

    public function store(CreateVendorProfileRequest $request): JsonResponse
    {
        $user    = $this->userService->findOrFail($request->string('user_id')->toString());
        $profile = $this->vendorService->create($user, $request->safe()->except('user_id'));

        return $this->successResponse(
            new VendorProfileResource($profile->load('user')),
            'Vendor profile created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateVendorProfileRequest $request, string $uuid): JsonResponse
    {
        $profile = $this->vendorService->findOrFail($uuid);
        $updated = $this->vendorService->update($profile, $request->validated());

        return $this->successResponse(
            new VendorProfileResource($updated->load('user')),
            'Vendor profile updated successfully.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $profile = $this->vendorService->findOrFail($uuid);
        $this->vendorService->delete($profile);

        return $this->successResponse(null, 'Vendor profile deleted successfully.');
    }

    public function assignToMaintenance(AssignVendorRequest $request, string $maintenanceUuid): JsonResponse
    {
        $maintenanceRequest = $this->maintenanceService->findOrFail($maintenanceUuid);
        $vendor             = $this->userService->findOrFail($request->string('vendor_id')->toString());

        $updated = $this->vendorService->assignToMaintenanceRequest($maintenanceRequest, $vendor);

        return $this->successResponse(
            new MaintenanceRequestResource($updated->load(['property', 'submitter', 'assignee', 'vendor'])),
            'Vendor assigned successfully.'
        );
    }

}
