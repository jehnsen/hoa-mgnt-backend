<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'unit_number'    => ['required', 'string', 'max:20', 'unique:properties,unit_number'],
            'block'          => ['nullable', 'string', 'max:10'],
            'floor'          => ['nullable', 'integer', 'min:0', 'max:200'],
            'street_address' => ['required', 'string', 'max:255'],
            'type'           => ['required', 'string', 'max:50'],
            'monthly_dues'   => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'late_fee_rate'  => ['required', 'numeric', 'min:0', 'max:1'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }
}
