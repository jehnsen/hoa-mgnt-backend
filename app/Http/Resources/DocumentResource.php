<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Document
 */
class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->uuid,
            'uploaded_by'    => new UserResource($this->whenLoaded('uploader')),
            'title'          => $this->title,
            'description'    => $this->description,
            'category'       => $this->category->value,
            'category_label' => $this->category->label(),
            'file_name'      => $this->file_name,
            'file_size'      => $this->file_size,
            'mime_type'      => $this->mime_type,
            'download_url'   => url("/api/v1/documents/{$this->uuid}/download"),
            'is_public'      => $this->is_public,
            'published_at'   => $this->published_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
