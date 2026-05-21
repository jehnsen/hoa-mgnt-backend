<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Property
 */
class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->uuid,
            'unit_number'   => $this->unit_number,
            'block'         => $this->block,
            'floor'         => $this->floor,
            'street_address'=> $this->street_address,
            'type'          => $this->type,
            'monthly_dues'  => $this->monthly_dues,
            'late_fee_rate' => $this->late_fee_rate,
            'is_active'     => $this->is_active,
            'residents'     => UserResource::collection($this->whenLoaded('residents')),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
