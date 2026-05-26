<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\InvoiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $type = $this->input('type', InvoiceType::MonthlyDues->value);
        $isMonthlyDues = $type === InvoiceType::MonthlyDues->value;

        return [
            'property_id'  => ['required', 'string', 'exists:properties,uuid'],
            'type'         => ['sometimes', Rule::enum(InvoiceType::class)],
            'period_month' => [
                Rule::requiredIf($isMonthlyDues),
                'nullable',
                'date_format:Y-m',
            ],
            'base_amount'  => [
                Rule::requiredIf(! $isMonthlyDues),
                'nullable',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
            ],
            'description'  => ['nullable', 'string', 'max:500'],
            'due_at'       => [
                Rule::requiredIf(! $isMonthlyDues),
                'nullable',
                'date',
            ],
        ];
    }
}
