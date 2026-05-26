<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ViolationAppeal
 */
class ViolationAppealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->uuid,
            'violation_id'    => $this->violation?->uuid,
            'appellant'       => new UserResource($this->whenLoaded('appellant')),
            'notes'           => $this->notes,
            'evidence_images' => $this->evidence_images ?? [],
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
