<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\EmergencyContact
 */
class EmergencyContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->uuid,
            'user'         => new UserResource($this->whenLoaded('user')),
            'name'         => $this->name,
            'relationship' => $this->relationship,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'is_primary'   => $this->is_primary,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
