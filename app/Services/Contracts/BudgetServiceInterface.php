<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\HoaBudget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetServiceInterface
{
    /** @return Collection<int, HoaBudget> */
    public function forYear(int $year): Collection;

    public function findOrFail(string $uuid): HoaBudget;

    /** @param array<string, mixed> $data */
    public function save(array $data): HoaBudget;

    public function delete(HoaBudget $budget): void;

    /** Returns per-category budgeted vs actual collected for the fiscal year. */
    public function summary(int $year): array;
}
