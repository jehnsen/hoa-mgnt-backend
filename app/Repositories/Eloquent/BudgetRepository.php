<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BudgetCategory;
use App\Models\HoaBudget;
use App\Repositories\Contracts\BudgetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BudgetRepository implements BudgetRepositoryInterface
{
    public function __construct(private readonly HoaBudget $model) {}

    public function forYear(int $year): Collection
    {
        return $this->model->newQuery()
                           ->where('fiscal_year', $year)
                           ->orderBy('category')
                           ->get();
    }

    public function findByUuid(string $uuid): ?HoaBudget
    {
        return $this->model->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByYearAndCategory(int $year, BudgetCategory $category): ?HoaBudget
    {
        return $this->model->newQuery()
                           ->where('fiscal_year', $year)
                           ->where('category', $category)
                           ->first();
    }

    public function create(array $data): HoaBudget
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(HoaBudget $budget, array $data): HoaBudget
    {
        $budget->fill($data)->save();

        return $budget->refresh();
    }

    public function delete(HoaBudget $budget): void
    {
        $budget->delete();
    }
}
