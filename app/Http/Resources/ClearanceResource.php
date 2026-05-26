<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Clearance
 */
class ClearanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->uuid,
            'property'         => new PropertyResource($this->whenLoaded('property')),
            'requested_by'     => new UserResource($this->whenLoaded('requester')),
            'issued_by'        => new UserResource($this->whenLoaded('issuer')),
            'purpose'          => $this->purpose?->value,
            'purpose_label'    => $this->purpose?->label(),
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'notes'            => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'valid_until'      => $this->valid_until?->toDateString(),
            'issued_at'        => $this->issued_at?->toIso8601String(),
            'rejected_at'      => $this->rejected_at?->toIso8601String(),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
