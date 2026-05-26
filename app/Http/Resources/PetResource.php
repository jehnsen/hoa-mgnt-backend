<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Pet
 */
class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->uuid,
            'property'           => new PropertyResource($this->whenLoaded('property')),
            'registered_by'      => new UserResource($this->whenLoaded('registrant')),
            'name'               => $this->name,
            'type'               => $this->type?->value,
            'type_label'         => $this->type?->label(),
            'breed'              => $this->breed,
            'color'              => $this->color,
            'is_vaccinated'      => $this->is_vaccinated,
            'vaccination_record' => $this->vaccination_record,
            'registration_fee'   => $this->registration_fee,
            'is_active'          => $this->is_active,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
