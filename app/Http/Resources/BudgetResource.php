<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\HoaBudget */
class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->uuid,
            'fiscal_year'     => $this->fiscal_year,
            'category'        => $this->category->value,
            'category_label'  => $this->category->label(),
            'budgeted_amount' => (float) $this->budgeted_amount,
            'notes'           => $this->notes,
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
