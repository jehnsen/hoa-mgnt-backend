<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AnnouncementServiceInterface
{
    /** @return LengthAwarePaginator<Announcement> */
    public function listPublished(?AnnouncementAudience $audience, int $perPage = 20): LengthAwarePaginator;

    /** @return LengthAwarePaginator<Announcement> */
    public function listAll(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Announcement;

    /** @param array<string, mixed> $data */
    public function create(array $data, User $author): Announcement;

    /** @param array<string, mixed> $data */
    public function update(string $uuid, array $data): Announcement;

    public function delete(string $uuid): void;
}
