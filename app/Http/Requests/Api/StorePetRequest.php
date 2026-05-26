<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\PetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'        => ['required', 'string', 'exists:properties,uuid'],
            'name'               => ['required', 'string', 'max:100'],
            'type'               => ['required', Rule::enum(PetType::class)],
            'breed'              => ['nullable', 'string', 'max:100'],
            'color'              => ['nullable', 'string', 'max:50'],
            'is_vaccinated'      => ['sometimes', 'boolean'],
            'vaccination_record' => ['nullable', 'string', 'max:255'],
            'registration_fee'   => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
        ];
    }
}
