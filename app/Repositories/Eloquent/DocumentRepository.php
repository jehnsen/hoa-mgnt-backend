<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function __construct(private readonly Document $model) {}

    public function findByUuid(string $uuid): ?Document
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('uploader')
                           ->first();
    }

    public function paginateFiltered(?DocumentCategory $category, bool $publicOnly, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('uploader');

        if ($category !== null) {
            $query->where('category', $category);
        }

        if ($publicOnly) {
            $query->public();
        }

        return $query->orderByDesc('published_at')->orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): Document
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Document $document, array $data): Document
    {
        $document->fill($data)->save();

        return $document->refresh();
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }
}
