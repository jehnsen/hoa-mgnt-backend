<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\DocumentCategory;
use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentRepositoryInterface
{
    public function findByUuid(string $uuid): ?Document;

    /** @return LengthAwarePaginator<Document> */
    public function paginateFiltered(?DocumentCategory $category, bool $publicOnly, int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): Document;

    /** @param array<string, mixed> $data */
    public function update(Document $document, array $data): Document;

    public function delete(Document $document): void;
}
