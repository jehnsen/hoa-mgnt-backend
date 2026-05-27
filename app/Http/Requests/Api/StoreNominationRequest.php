<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\BoardPositionTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNominationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nominee_id'          => ['required', 'string', 'exists:users,uuid'],
            'position_value'      => ['required', Rule::enum(BoardPositionTitle::class)],
            'candidate_statement' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
