<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Slim controller — all domain logic delegated to UserServiceInterface.
 */
final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection($this->userService->list($this->perPage()));
    }

    public function show(string $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->userService->findOrFail($user))
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return $this->successResponse(
            new UserResource($user),
            'User created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateUserRequest $request, string $user): JsonResponse
    {
        return $this->successResponse(
            new UserResource($this->userService->update($user, $request->validated())),
            'User updated successfully.'
        );
    }

    public function destroy(string $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->successResponse(null, 'User deleted successfully.');
    }

}
