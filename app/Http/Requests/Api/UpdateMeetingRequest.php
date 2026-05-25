<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\MeetingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'string', 'min:5', 'max:200'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:5000'],
            'location'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'scheduled_at' => ['sometimes', 'date'],
            'agenda'       => ['sometimes', 'nullable', 'array'],
            'agenda.*'     => ['required_with:agenda', 'string', 'max:300'],
            'minutes'      => ['sometimes', 'nullable', 'string'],
            'status'       => ['sometimes', Rule::enum(MeetingStatus::class)],
        ];
    }
}
