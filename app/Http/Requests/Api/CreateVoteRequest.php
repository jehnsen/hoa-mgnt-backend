<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'question'     => ['required', 'string', 'min:5', 'max:300'],
            'options'      => ['required', 'array', 'min:2', 'max:10'],
            'options.*'    => ['required', 'string', 'max:300'],
            'closes_at'    => ['nullable', 'date', 'after:now'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ];
    }
}
