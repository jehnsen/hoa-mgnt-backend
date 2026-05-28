<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\User;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\DocumentServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DocumentService implements DocumentServiceInterface
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documentRepository,
        private readonly AuditLogger                 $auditLogger,
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

    public function create(array $data, User $uploader, UploadedFile $file): Document
    {
        $path = $file->store(
            'documents/' . now()->format('Y/m'),
            'local'
        );

        $document = $this->documentRepository->create(array_merge($data, [
            'uploaded_by' => $uploader->id,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType() ?? $file->getClientMimeType(),
        ]));

        $this->auditLogger->log(
            'document',
            $document->uuid,
            'document_uploaded',
            null,
            ['file_name' => $document->file_name, 'category' => $data['category'] ?? null],
        );

        return $document;
    }

    public function download(string $uuid, User $viewer): Document
    {
        return $this->findOrFail($uuid, $viewer);
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

        $this->auditLogger->log(
            'document',
            $document->uuid,
            'document_deleted',
            ['file_name' => $document->file_name],
        );

        $this->documentRepository->delete($document);

        // Remove the physical file after the record is soft-deleted
        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
    }
}
