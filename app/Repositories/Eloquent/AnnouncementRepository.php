<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Repositories\Contracts\AnnouncementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function __construct(private readonly Announcement $model) {}

    public function findByUuid(string $uuid): ?Announcement
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('author')
                           ->first();
    }

    public function paginatePublished(?AnnouncementAudience $audience, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
                             ->published()
                             ->with('author')
                             ->orderByDesc('is_pinned')
                             ->orderByDesc('published_at');

        if ($audience !== null) {
            $query->where(fn ($q) => $q->where('audience', AnnouncementAudience::All->value)
                                       ->orWhere('audience', $audience->value));
        }

        return $query->paginate($perPage);
    }

    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->with('author')
                           ->orderByDesc('created_at')
                           ->paginate($perPage);
    }

    public function create(array $data): Announcement
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Announcement $announcement, array $data): Announcement
    {
        $announcement->fill($data)->save();

        return $announcement->refresh();
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }
}
