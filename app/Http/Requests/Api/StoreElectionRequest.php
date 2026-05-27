<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreElectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['sometimes', 'nullable', 'string', 'max:5000'],
            'nomination_deadline' => ['sometimes', 'nullable', 'date'],
            'voting_open_at'      => ['sometimes', 'nullable', 'date'],
            'voting_close_at'     => ['sometimes', 'nullable', 'date', 'after:voting_open_at'],
        ];
    }
}
