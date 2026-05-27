<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ElectionNomination */
class ElectionNominationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->uuid,
            'election_id'         => $this->election?->uuid,
            'nominee'             => new UserResource($this->whenLoaded('nominee')),
            'nominator'           => new UserResource($this->whenLoaded('nominator')),
            'position'            => $this->position_value->value,
            'position_label'      => $this->position_value->label(),
            'candidate_statement' => $this->candidate_statement,
            'status'              => $this->status->value,
            'status_label'        => $this->status->label(),
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
