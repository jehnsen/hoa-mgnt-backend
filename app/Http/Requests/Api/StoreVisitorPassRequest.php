<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorPassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'   => ['required', 'string', 'exists:properties,uuid'],
            'visitor_name'  => ['required', 'string', 'max:100'],
            'vehicle_plate' => ['sometimes', 'nullable', 'string', 'max:20'],
            'expected_at'   => ['required', 'date', 'after_or_equal:now'],
            'expires_at'    => ['required', 'date', 'after:expected_at'],
            'purpose'       => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
