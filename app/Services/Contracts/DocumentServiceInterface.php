<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface DocumentServiceInterface
{
    /** @return LengthAwarePaginator<Document> */
    public function list(?DocumentCategory $category, User $viewer, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid, User $viewer): Document;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, User $uploader, UploadedFile $file): Document;

    public function download(string $uuid, User $viewer): Document;

    /** @param array<string, mixed> $data */
    public function update(string $uuid, array $data): Document;

    public function delete(string $uuid): void;
}
