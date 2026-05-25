<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Meeting
 */
class MeetingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->uuid,
            'organizer'    => new UserResource($this->whenLoaded('organizer')),
            'title'        => $this->title,
            'description'  => $this->description,
            'location'     => $this->location,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'agenda'       => $this->agenda ?? [],
            'minutes'      => $this->minutes,
            'votes'        => MeetingVoteResource::collection($this->whenLoaded('votes')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
