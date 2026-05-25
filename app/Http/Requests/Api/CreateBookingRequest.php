<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'amenity_id'  => ['required', 'string', 'exists:amenities,uuid'],
            'property_id' => ['required', 'string', 'exists:properties,uuid'],
            'title'       => ['required', 'string', 'min:3', 'max:200'],
            'start_at'    => ['required', 'date', 'after:now'],
            'end_at'      => ['required', 'date', 'after:start_at'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }
}
