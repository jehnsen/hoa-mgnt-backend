<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\MeetingVote
 */
class MeetingVoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->uuid,
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'question'     => $this->question,
            'options'      => $this->options,
            'closes_at'    => $this->closes_at?->toIso8601String(),
            'is_anonymous' => $this->is_anonymous,
            'is_open'      => $this->isOpen(),
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
