<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\RecurringMaintenanceSchedule
 */
class RecurringMaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->uuid,
            'property'           => new PropertyResource($this->whenLoaded('property')),
            'assignee'           => new UserResource($this->whenLoaded('assignee')),
            'category'           => $this->category->value,
            'category_label'     => $this->category->label(),
            'title'              => $this->title,
            'description'        => $this->description,
            'priority'           => $this->priority->value,
            'priority_label'     => $this->priority->label(),
            'frequency'          => $this->frequency->value,
            'frequency_label'    => $this->frequency->label(),
            'frequency_interval' => $this->frequency_interval,
            'estimated_cost'     => $this->estimated_cost,
            'next_due_at'        => $this->next_due_at?->toDateString(),
            'last_run_at'        => $this->last_run_at?->toDateString(),
            'is_active'          => $this->is_active,
            'created_at'         => $this->created_at?->toIso8601String(),
        ];
    }
}
