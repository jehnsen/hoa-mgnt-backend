<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'min:5', 'max:200'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'location'     => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'agenda'       => ['nullable', 'array'],
            'agenda.*'     => ['required', 'string', 'max:300'],
        ];
    }
}
