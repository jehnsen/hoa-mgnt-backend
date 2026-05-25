<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AnnouncementRepositoryInterface
{
    public function findByUuid(string $uuid): ?Announcement;

    /** @return LengthAwarePaginator<Announcement> */
    public function paginatePublished(?AnnouncementAudience $audience, int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Announcement> */
    public function paginateAll(int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): Announcement;

    /** @param array<string, mixed> $data */
    public function update(Announcement $announcement, array $data): Announcement;

    public function delete(Announcement $announcement): void;
}
