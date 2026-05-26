<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\VendorProfile
 */
class VendorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->uuid,
            'user'           => new UserResource($this->whenLoaded('user')),
            'company_name'   => $this->company_name,
            'service_type'   => $this->service_type,
            'license_number' => $this->license_number,
            'is_active'      => $this->is_active,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
