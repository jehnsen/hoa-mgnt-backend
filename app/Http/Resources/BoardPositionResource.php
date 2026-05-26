<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BoardPosition
 */
class BoardPositionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->uuid,
            'user'           => new UserResource($this->whenLoaded('user')),
            'position'       => $this->position->value,
            'position_label' => $this->position->label(),
            'term_start'     => $this->term_start?->toDateString(),
            'term_end'       => $this->term_end?->toDateString(),
            'is_active'      => $this->is_active,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
