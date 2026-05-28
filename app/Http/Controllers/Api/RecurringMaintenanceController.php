<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRecurringMaintenanceRequest;
use App\Http\Requests\Api\UpdateRecurringMaintenanceRequest;
use App\Http\Resources\MaintenanceRequestResource;
use App\Http\Resources\RecurringMaintenanceResource;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\RecurringMaintenanceServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class RecurringMaintenanceController extends Controller
{
    public function __construct(
        private readonly RecurringMaintenanceServiceInterface $recurringService,
        private readonly PropertyServiceInterface             $propertyService,
        private readonly UserServiceInterface                 $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $activeOnly = request()->boolean('active_only', false);

        return RecurringMaintenanceResource::collection(
            $this->recurringService->list($activeOnly, $this->perPage())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new RecurringMaintenanceResource($this->recurringService->findOrFail($uuid))
        );
    }

    public function store(StoreRecurringMaintenanceRequest $request): JsonResponse
    {
        $data = $request->safe()->all();

        if (isset($data['property_id'])) {
            $property           = $this->propertyService->findOrFail($data['property_id']);
            $data['property_id'] = $property->id;
        }

        if (isset($data['assigned_to'])) {
            $assignee           = $this->userService->findOrFail($data['assigned_to']);
            $data['assigned_to'] = $assignee->id;
        }

        return $this->successResponse(
            new RecurringMaintenanceResource($this->recurringService->create($data)),
            'Recurring schedule created.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateRecurringMaintenanceRequest $request, string $uuid): JsonResponse
    {
        $schedule = $this->recurringService->findOrFail($uuid);
        $data     = $request->safe()->all();

        if (isset($data['property_id'])) {
            $property           = $this->propertyService->findOrFail($data['property_id']);
            $data['property_id'] = $property->id;
        }

        if (isset($data['assigned_to'])) {
            $assignee           = $this->userService->findOrFail($data['assigned_to']);
            $data['assigned_to'] = $assignee->id;
        }

        return $this->successResponse(
            new RecurringMaintenanceResource($this->recurringService->update($schedule, $data)),
            'Recurring schedule updated.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->recurringService->delete($this->recurringService->findOrFail($uuid));

        return $this->successResponse(null, 'Recurring schedule deleted.');
    }

    public function spawn(string $uuid): JsonResponse
    {
        $schedule = $this->recurringService->findOrFail($uuid);
        $request  = $this->recurringService->spawn($schedule);

        return $this->successResponse(
            new MaintenanceRequestResource($request->load(['property', 'assignee'])),
            'Maintenance request spawned.',
            Response::HTTP_CREATED
        );
    }

}
