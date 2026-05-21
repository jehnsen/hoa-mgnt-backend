<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Route parameter is now a UUID string; exclude by uuid column for uniqueness check
        $propertyUuid = $this->route('property');

        return [
            'unit_number'    => ['sometimes', 'string', 'max:20', "unique:properties,unit_number,{$propertyUuid},uuid"],
            'block'          => ['sometimes', 'nullable', 'string', 'max:10'],
            'floor'          => ['sometimes', 'nullable', 'integer', 'min:0', 'max:200'],
            'street_address' => ['sometimes', 'string', 'max:255'],
            'type'           => ['sometimes', 'string', 'max:50'],
            'monthly_dues'   => ['sometimes', 'numeric', 'min:0', 'max:999999.99'],
            'late_fee_rate'  => ['sometimes', 'numeric', 'min:0', 'max:1'],
            'is_active'      => ['sometimes', 'boolean'],
        ];
    }
}
