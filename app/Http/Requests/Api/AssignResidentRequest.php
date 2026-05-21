<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AssignResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_uuid'            => ['required', 'string', 'exists:users,uuid'],
            'move_in_at'           => ['required', 'date'],
            'move_out_at'          => ['nullable', 'date', 'after:move_in_at'],
            'is_primary_resident'  => ['sometimes', 'boolean'],
        ];
    }
}
