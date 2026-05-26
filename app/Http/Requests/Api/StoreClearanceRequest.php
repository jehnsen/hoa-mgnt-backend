<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\ClearancePurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClearanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'string', 'exists:properties,uuid'],
            'purpose'     => ['required', Rule::enum(ClearancePurpose::class)],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ];
    }
}
