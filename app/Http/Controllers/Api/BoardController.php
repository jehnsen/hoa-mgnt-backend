<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBoardPositionRequest;
use App\Http\Requests\Api\UpdateBoardPositionRequest;
use App\Http\Resources\BoardPositionResource;
use App\Services\Contracts\BoardServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class BoardController extends Controller
{
    public function __construct(
        private readonly BoardServiceInterface $boardService,
        private readonly UserServiceInterface  $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return BoardPositionResource::collection($this->boardService->listActive());
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(new BoardPositionResource($this->boardService->findOrFail($uuid)));
    }

    public function store(StoreBoardPositionRequest $request): JsonResponse
    {
        $user     = $this->userService->findOrFail($request->string('user_uuid')->toString());
        $position = $this->boardService->assign(array_merge(
            $request->safe()->except('user_uuid'),
            ['user_id' => $user->id]
        ));

        return $this->successResponse(
            new BoardPositionResource($position->load('user')),
            'Board position assigned.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateBoardPositionRequest $request, string $uuid): JsonResponse
    {
        $position = $this->boardService->findOrFail($uuid);
        $updated  = $this->boardService->update($position, $request->validated());

        return $this->successResponse(new BoardPositionResource($updated->load('user')), 'Board position updated.');
    }

    public function vacate(string $uuid): JsonResponse
    {
        $position = $this->boardService->findOrFail($uuid);
        $vacated  = $this->boardService->vacate($position);

        return $this->successResponse(new BoardPositionResource($vacated->load('user')), 'Board position vacated.');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->boardService->delete($this->boardService->findOrFail($uuid));

        return $this->successResponse(null, 'Board position removed.');
    }

}
