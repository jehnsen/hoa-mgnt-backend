<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CommitteeMember
 */
class CommitteeMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->uuid,
            'user'      => new UserResource($this->whenLoaded('user')),
            'role'      => $this->role->value,
            'role_label'=> $this->role->label(),
            'joined_at' => $this->joined_at?->toDateString(),
            'left_at'   => $this->left_at?->toDateString(),
        ];
    }
}
