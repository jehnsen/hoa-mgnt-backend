<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Amenity
 */
class AmenityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->uuid,
            'name'        => $this->name,
            'description' => $this->description,
            'location'    => $this->location,
            'capacity'    => $this->capacity,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
