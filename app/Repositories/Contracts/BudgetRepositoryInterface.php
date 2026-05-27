<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\BudgetCategory;
use App\Models\HoaBudget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetRepositoryInterface
{
    /** @return Collection<int, HoaBudget> */
    public function forYear(int $year): Collection;

    public function findByUuid(string $uuid): ?HoaBudget;

    public function findByYearAndCategory(int $year, BudgetCategory $category): ?HoaBudget;

    /** @param array<string, mixed> $data */
    public function create(array $data): HoaBudget;

    /** @param array<string, mixed> $data */
    public function update(HoaBudget $budget, array $data): HoaBudget;

    public function delete(HoaBudget $budget): void;
}
