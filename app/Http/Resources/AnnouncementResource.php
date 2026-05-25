<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Announcement
 */
class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->uuid,
            'author'       => new UserResource($this->whenLoaded('author')),
            'title'        => $this->title,
            'body'         => $this->body,
            'audience'     => $this->audience->value,
            'audience_label' => $this->audience->label(),
            'is_pinned'    => $this->is_pinned,
            'published_at' => $this->published_at?->toIso8601String(),
            'expires_at'   => $this->expires_at?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
