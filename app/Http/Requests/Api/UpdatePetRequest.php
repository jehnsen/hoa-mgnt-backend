<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\PetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'               => ['sometimes', 'string', 'max:100'],
            'type'               => ['sometimes', Rule::enum(PetType::class)],
            'breed'              => ['sometimes', 'nullable', 'string', 'max:100'],
            'color'              => ['sometimes', 'nullable', 'string', 'max:50'],
            'is_vaccinated'      => ['sometimes', 'boolean'],
            'vaccination_record' => ['sometimes', 'nullable', 'string', 'max:255'],
            'registration_fee'   => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:99999.99'],
            'is_active'          => ['sometimes', 'boolean'],
        ];
    }
}
