<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\BudgetCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() || $this->user()?->isBoardMember();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'fiscal_year'     => ['required', 'integer', 'min:2000', 'max:2100'],
            'category'        => ['required', Rule::enum(BudgetCategory::class)],
            'budgeted_amount' => ['required', 'numeric', 'min:0'],
            'notes'           => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
