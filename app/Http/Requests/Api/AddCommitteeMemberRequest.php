<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\CommitteeRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddCommitteeMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'string', 'exists:users,uuid'],
            'role'      => ['nullable', new Enum(CommitteeRole::class)],
            'joined_at' => ['nullable', 'date'],
        ];
    }
}
