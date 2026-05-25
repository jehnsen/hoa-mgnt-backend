<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\User;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use App\Services\Contracts\AnnouncementServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AnnouncementService implements AnnouncementServiceInterface
{
    public function __construct(
        private readonly AnnouncementRepositoryInterface $announcementRepository,
    ) {}

    public function listPublished(?AnnouncementAudience $audience, int $perPage = 20): LengthAwarePaginator
    {
        return $this->announcementRepository->paginatePublished($audience, $perPage);
    }

    public function listAll(int $perPage = 20): LengthAwarePaginator
    {
        return $this->announcementRepository->paginateAll($perPage);
    }

    public function findOrFail(string $uuid): Announcement
    {
        $announcement = $this->announcementRepository->findByUuid($uuid);

        if ($announcement === null) {
            throw new NotFoundHttpException("Announcement [{$uuid}] not found.");
        }

        return $announcement;
    }

    public function create(array $data, User $author): Announcement
    {
        return $this->announcementRepository->create(
            array_merge($data, ['author_id' => $author->id])
        );
    }

    public function update(string $uuid, array $data): Announcement
    {
        $announcement = $this->findOrFail($uuid);

        return $this->announcementRepository->update($announcement, $data);
    }

    public function delete(string $uuid): void
    {
        $announcement = $this->findOrFail($uuid);

        $this->announcementRepository->delete($announcement);
    }
}
