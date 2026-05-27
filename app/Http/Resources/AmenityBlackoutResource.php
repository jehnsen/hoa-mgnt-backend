<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AmenityBlackout
 */
class AmenityBlackoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->uuid,
            'amenity'    => new AmenityResource($this->whenLoaded('amenity')),
            'start_at'   => $this->start_at?->toIso8601String(),
            'end_at'     => $this->end_at?->toIso8601String(),
            'reason'     => $this->reason,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
