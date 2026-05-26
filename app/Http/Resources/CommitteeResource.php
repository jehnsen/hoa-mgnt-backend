<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Committee
 */
class CommitteeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->uuid,
            'name'         => $this->name,
            'description'  => $this->description,
            'is_active'    => $this->is_active,
            'member_count' => $this->whenCounted('activeMembers'),
            'members'      => CommitteeMemberResource::collection($this->whenLoaded('activeMembers')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
