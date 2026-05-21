<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'property_id'  => ['required', 'string', 'exists:properties,uuid'],
            'period_month' => ['required', 'date_format:Y-m'],
        ];
    }
}
