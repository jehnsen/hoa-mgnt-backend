<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\VisitorPass */
class VisitorPassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->uuid,
            'property'      => new PropertyResource($this->whenLoaded('property')),
            'resident'      => new UserResource($this->whenLoaded('resident')),
            'visitor_name'  => $this->visitor_name,
            'vehicle_plate' => $this->vehicle_plate,
            'expected_at'   => $this->expected_at?->toIso8601String(),
            'expires_at'    => $this->expires_at?->toIso8601String(),
            'purpose'       => $this->purpose,
            'access_code'   => $this->access_code,
            'is_used'       => $this->is_used,
            'used_at'       => $this->used_at?->toIso8601String(),
            'is_expired'    => $this->isExpired(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
