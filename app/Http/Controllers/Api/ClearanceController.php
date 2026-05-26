<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ClearanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClearanceRequest;
use App\Http\Requests\Api\UpdateClearanceStatusRequest;
use App\Http\Resources\ClearanceResource;
use App\Services\Contracts\ClearanceServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ClearanceController extends Controller
{
    public function __construct(
        private readonly ClearanceServiceInterface $clearanceService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ClearanceResource::collection(
            $this->clearanceService->list(request()->user())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $clearance = $this->clearanceService->findOrFail($uuid);

        return $this->ok(new ClearanceResource($clearance));
    }

    public function store(StoreClearanceRequest $request): JsonResponse
    {
        $clearance = $this->clearanceService->create($request->validated(), $request->user());

        return $this->ok(
            new ClearanceResource($clearance->load(['property', 'requester'])),
            'Clearance request submitted.',
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(UpdateClearanceStatusRequest $request, string $uuid): JsonResponse
    {
        $clearance  = $this->clearanceService->findOrFail($uuid);
        $newStatus  = ClearanceStatus::from($request->string('status')->toString());

        $updated = $this->clearanceService->updateStatus(
            $clearance,
            $newStatus,
            $request->validated(),
            $request->user()
        );

        return $this->ok(
            new ClearanceResource($updated->load(['property', 'requester', 'issuer'])),
            'Clearance status updated.'
        );
    }

    private function ok(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
