<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEmergencyContactRequest;
use App\Http\Requests\Api\UpdateEmergencyContactRequest;
use App\Http\Resources\EmergencyContactResource;
use App\Services\Contracts\EmergencyContactServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class EmergencyContactController extends Controller
{
    public function __construct(
        private readonly EmergencyContactServiceInterface $contactService,
    ) {}

    public function indexForUser(string $userUuid): JsonResponse
    {
        $contacts = $this->contactService->listForUser($userUuid, request()->user());

        return $this->ok(EmergencyContactResource::collection($contacts));
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->ok(new EmergencyContactResource(
            $this->contactService->findOrFail($uuid)
        ));
    }

    public function store(StoreEmergencyContactRequest $request, string $userUuid): JsonResponse
    {
        $contact = $this->contactService->create($userUuid, $request->validated(), $request->user());

        return $this->ok(
            new EmergencyContactResource($contact),
            'Emergency contact added.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateEmergencyContactRequest $request, string $uuid): JsonResponse
    {
        $contact = $this->contactService->findOrFail($uuid);
        $updated = $this->contactService->update($contact, $request->validated());

        return $this->ok(new EmergencyContactResource($updated), 'Emergency contact updated.');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->contactService->delete($this->contactService->findOrFail($uuid));

        return $this->ok(null, 'Emergency contact removed.');
    }

    private function ok(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
