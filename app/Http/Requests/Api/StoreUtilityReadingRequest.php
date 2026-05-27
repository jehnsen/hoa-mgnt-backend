<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\UtilityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUtilityReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'      => ['required', 'string', 'exists:properties,uuid'],
            'utility_type'     => ['required', Rule::enum(UtilityType::class)],
            'meter_number'     => ['sometimes', 'nullable', 'string', 'max:50'],
            'previous_reading' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'current_reading'  => ['required', 'numeric', 'min:0'],
            'reading_date'     => ['required', 'date'],
            'rate_per_unit'    => ['required', 'numeric', 'min:0'],
            'fixed_charge'     => ['sometimes', 'numeric', 'min:0'],
            'notes'            => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
