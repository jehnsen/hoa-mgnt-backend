<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePetRequest;
use App\Http\Requests\Api\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Services\Contracts\PetServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class PetController extends Controller
{
    public function __construct(
        private readonly PetServiceInterface $petService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return PetResource::collection(
            $this->petService->list(request()->user(), $this->perPage())
        );
    }

    public function indexForProperty(string $propertyUuid): AnonymousResourceCollection
    {
        return PetResource::collection(
            $this->petService->listForProperty($propertyUuid, request()->user(), $this->perPage())
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(new PetResource($this->petService->findOrFail($uuid)));
    }

    public function store(StorePetRequest $request): JsonResponse
    {
        $pet = $this->petService->create($request->validated(), $request->user());

        return $this->successResponse(
            new PetResource($pet->load(['property', 'registrant'])),
            'Pet registered successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdatePetRequest $request, string $uuid): JsonResponse
    {
        $pet     = $this->petService->findOrFail($uuid);
        $updated = $this->petService->update($pet, $request->validated());

        return $this->successResponse(new PetResource($updated->load(['property', 'registrant'])), 'Pet updated.');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->petService->delete($this->petService->findOrFail($uuid));

        return $this->successResponse(null, 'Pet removed.');
    }

}
