<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\Contracts\DocumentServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentService implements DocumentServiceInterface
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documentRepository,
    ) {}

    public function list(?DocumentCategory $category, User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        $publicOnly = $viewer->isResident();

        return $this->documentRepository->paginateFiltered($category, $publicOnly, $perPage);
    }

    public function findOrFail(string $uuid, User $viewer): Document
    {
        $document = $this->documentRepository->findByUuid($uuid);

        if ($document === null) {
            throw new NotFoundHttpException("Document [{$uuid}] not found.");
        }

        if ($viewer->isResident() && (! $document->is_public || $document->published_at === null)) {
            throw new NotFoundHttpException("Document [{$uuid}] not found.");
        }

        return $document;
    }

    public function create(array $data, User $uploader): Document
    {
        return $this->documentRepository->create(
            array_merge($data, ['uploaded_by' => $uploader->id])
        );
    }

    public function update(string $uuid, array $data): Document
    {
        $document = $this->documentRepository->findByUuid($uuid);

        if ($document === null) {
            throw new NotFoundHttpException("Document [{$uuid}] not found.");
        }

        return $this->documentRepository->update($document, $data);
    }

    public function delete(string $uuid): void
    {
        $document = $this->documentRepository->findByUuid($uuid);

        if ($document === null) {
            throw new NotFoundHttpException("Document [{$uuid}] not found.");
        }

        $this->documentRepository->delete($document);
    }
}
