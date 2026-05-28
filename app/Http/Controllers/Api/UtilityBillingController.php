<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUtilityReadingRequest;
use App\Http\Resources\UtilityMeterReadingResource;
use App\Services\Contracts\PropertyServiceInterface;
use App\Services\Contracts\UtilityBillingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class UtilityBillingController extends Controller
{
    public function __construct(
        private readonly UtilityBillingServiceInterface $utilityService,
        private readonly PropertyServiceInterface       $propertyService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return UtilityMeterReadingResource::collection(
            $this->utilityService->list(
                propertyUuid: request()->string('property_id')->toString() ?: null,
                utilityType:  request()->string('utility_type')->toString() ?: null,
                perPage:      $this->perPage(),
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new UtilityMeterReadingResource($this->utilityService->findOrFail($uuid))
        );
    }

    public function store(StoreUtilityReadingRequest $request): JsonResponse
    {
        $property = $this->propertyService->findOrFail($request->string('property_id')->toString());

        $reading = $this->utilityService->recordReading(
            $property,
            $request->safe()->except('property_id'),
            $request->user()
        );

        return $this->successResponse(
            new UtilityMeterReadingResource($reading->load(['property', 'reader'])),
            'Meter reading recorded.',
            Response::HTTP_CREATED
        );
    }

    public function generateBill(string $uuid): JsonResponse
    {
        $reading = $this->utilityService->findOrFail($uuid);
        $invoice = $this->utilityService->generateBill($reading);

        return $this->successResponse(
            ['invoice_id' => $invoice->uuid, 'total_amount' => $invoice->total_amount],
            'Utility bill generated.'
        );
    }

}
