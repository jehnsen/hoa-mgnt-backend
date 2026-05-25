<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\MaintenanceRequest
 */
class MaintenanceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->uuid,
            'property'         => new PropertyResource($this->whenLoaded('property')),
            'submitter'        => new UserResource($this->whenLoaded('submitter')),
            'assignee'         => new UserResource($this->whenLoaded('assignee')),
            'category'         => $this->category->value,
            'category_label'   => $this->category->label(),
            'title'            => $this->title,
            'description'      => $this->description,
            'priority'         => $this->priority->value,
            'priority_label'   => $this->priority->label(),
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'resolution_notes' => $this->resolution_notes,
            'resolved_at'      => $this->resolved_at?->toIso8601String(),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
