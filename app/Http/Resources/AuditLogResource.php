<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\AuditLog
 */
class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->uuid,
            'auditable_type'  => $this->auditable_type,
            'auditable_uuid'  => $this->auditable_uuid,
            'action'          => $this->action,
            'actor'           => new UserResource($this->whenLoaded('actor')),
            'old_values'      => $this->old_values,
            'new_values'      => $this->new_values,
            'ip_address'      => $this->ip_address,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
