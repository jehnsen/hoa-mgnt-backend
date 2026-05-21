<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBulkDuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageFinancials() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'period_month' => ['required', 'date_format:Y-m', 'after_or_equal:2020-01'],
        ];
    }
}
