<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MeetingRepositoryInterface
{
    public function findByUuid(string $uuid): ?Meeting;

    /** @return LengthAwarePaginator<Meeting> */
    public function paginateFiltered(?MeetingStatus $status, int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): Meeting;

    /** @param array<string, mixed> $data */
    public function update(Meeting $meeting, array $data): Meeting;
}
