<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\AnnouncementAudience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateAnnouncementRequest;
use App\Http\Requests\Api\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Services\Contracts\AnnouncementServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementServiceInterface $announcementService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $user     = request()->user();
        $audience = $user?->isResident()
            ? AnnouncementAudience::Residents
            : ($user?->isBoardMember() ? AnnouncementAudience::BoardMembers : null);

        $list = ($user?->isSuperAdmin() || $user?->isBoardMember())
            ? $this->announcementService->listAll($this->perPage())
            : $this->announcementService->listPublished($audience, $this->perPage());

        return AnnouncementResource::collection($list);
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new AnnouncementResource($this->announcementService->findOrFail($uuid))
        );
    }

    public function store(CreateAnnouncementRequest $request): JsonResponse
    {
        $announcement = $this->announcementService->create(
            $request->safe()->all(),
            $request->user()
        );

        return $this->successResponse(
            new AnnouncementResource($announcement->load('author')),
            'Announcement created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateAnnouncementRequest $request, string $uuid): JsonResponse
    {
        return $this->successResponse(
            new AnnouncementResource($this->announcementService->update($uuid, $request->safe()->all())),
            'Announcement updated.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->announcementService->delete($uuid);

        return $this->successResponse(null, 'Announcement deleted.');
    }

}
