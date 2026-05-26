<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Vehicle
 */
class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->uuid,
            'property'         => new PropertyResource($this->whenLoaded('property')),
            'registered_by'    => new UserResource($this->whenLoaded('registrant')),
            'plate_number'     => $this->plate_number,
            'type'             => $this->type?->value,
            'type_label'       => $this->type?->label(),
            'make'             => $this->make,
            'model'            => $this->model,
            'color'            => $this->color,
            'year'             => $this->year,
            'sticker_number'   => $this->sticker_number,
            'gate_pass_number' => $this->gate_pass_number,
            'is_active'        => $this->is_active,
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
