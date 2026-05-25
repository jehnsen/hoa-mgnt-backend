<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\DocumentCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDocumentRequest;
use App\Http\Requests\Api\UploadDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\Contracts\DocumentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentServiceInterface $documentService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return DocumentResource::collection(
            $this->documentService->list(
                request()->enum('category', DocumentCategory::class),
                request()->user()
            )
        );
    }

    public function show(string $uuid): JsonResponse
    {
        return $this->successResponse(
            new DocumentResource($this->documentService->findOrFail($uuid, request()->user()))
        );
    }

    public function store(UploadDocumentRequest $request): JsonResponse
    {
        $document = $this->documentService->create($request->safe()->all(), $request->user());

        return $this->successResponse(
            new DocumentResource($document->load('uploader')),
            'Document uploaded successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateDocumentRequest $request, string $uuid): JsonResponse
    {
        return $this->successResponse(
            new DocumentResource($this->documentService->update($uuid, $request->safe()->all())),
            'Document updated.'
        );
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->documentService->delete($uuid);

        return $this->successResponse(null, 'Document deleted.');
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
