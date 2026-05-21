<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->uuid,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role->value,
            'role_label' => $this->role->label(),
            'phone'      => $this->phone,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
