<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        return $this->successResponse(new UserResource(request()->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->safe()->except('current_password', 'password', 'password_confirmation');

        if ($request->filled('password')) {
            if (! Hash::check($request->string('current_password')->toString(), $user->password)) {
                throw new HttpException(422, 'Current password is incorrect.');
            }

            $data['password'] = Hash::make($request->string('password')->toString());
        }

        $user->fill($data)->save();

        return $this->successResponse(new UserResource($user->refresh()), 'Profile updated successfully.');
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
