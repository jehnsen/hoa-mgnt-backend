<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BoardElection */
class ElectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->uuid,
            'title'               => $this->title,
            'description'         => $this->description,
            'status'              => $this->status->value,
            'status_label'        => $this->status->label(),
            'nomination_deadline' => $this->nomination_deadline?->toDateString(),
            'voting_open_at'      => $this->voting_open_at?->toIso8601String(),
            'voting_close_at'     => $this->voting_close_at?->toIso8601String(),
            'creator'             => new UserResource($this->whenLoaded('creator')),
            'nominations'         => ElectionNominationResource::collection($this->whenLoaded('nominations')),
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
