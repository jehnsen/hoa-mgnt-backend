<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudgetCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HoaBudget extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'fiscal_year',
        'category',
        'budgeted_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year'     => 'integer',
            'category'        => BudgetCategory::class,
            'budgeted_amount' => 'decimal:2',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
