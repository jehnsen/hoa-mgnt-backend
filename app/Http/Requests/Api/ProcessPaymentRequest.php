<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
            ],
            'payment_method' => [
                'required',
                new Enum(PaymentMethod::class),
            ],
            'transaction_reference' => [
                'nullable',
                'string',
                'max:100',
                // Uniqueness enforced at DB level; duplicate check here gives a friendly message
                Rule::unique('payments', 'transaction_reference'),
            ],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'paid_at'     => ['nullable', 'date', 'before_or_equal:now'],
            // Board member who physically received/recorded the payment; defaults to the auth user
            'received_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
