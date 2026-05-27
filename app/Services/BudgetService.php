<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BudgetCategory;
use App\Models\HoaBudget;
use App\Repositories\Contracts\BudgetRepositoryInterface;
use App\Services\Contracts\BudgetServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BudgetService implements BudgetServiceInterface
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function forYear(int $year): Collection
    {
        return $this->budgetRepository->forYear($year);
    }

    public function findOrFail(string $uuid): HoaBudget
    {
        $budget = $this->budgetRepository->findByUuid($uuid);

        if ($budget === null) {
            throw new NotFoundHttpException("Budget entry [{$uuid}] not found.");
        }

        return $budget;
    }

    public function save(array $data): HoaBudget
    {
        $year     = (int) $data['fiscal_year'];
        $category = BudgetCategory::from($data['category']);
        $existing = $this->budgetRepository->findByYearAndCategory($year, $category);

        if ($existing !== null) {
            return $this->budgetRepository->update($existing, $data);
        }

        return $this->budgetRepository->create($data);
    }

    public function delete(HoaBudget $budget): void
    {
        $this->budgetRepository->delete($budget);
    }

    public function summary(int $year): array
    {
        $budgets  = $this->budgetRepository->forYear($year)->keyBy(fn ($b) => $b->category->value);

        $yearStart = "{$year}-01-01";
        $yearEnd   = "{$year}-12-31";

        $actualRows = DB::table('invoices')
            ->whereNull('deleted_at')
            ->where('status', 'paid')
            ->whereBetween('period_month', [$yearStart, $yearEnd])
            ->selectRaw('type, SUM(total_amount) as total')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $typeToCategory = [
            'monthly_dues'        => BudgetCategory::Administrative,
            'special_assessment'  => BudgetCategory::Administrative,
            'water_bill'          => BudgetCategory::Utilities,
            'utility'             => BudgetCategory::Utilities,
            'amenity_booking_fee' => BudgetCategory::Amenities,
            'parking_fee'         => BudgetCategory::Administrative,
            'violation_fine'      => BudgetCategory::Administrative,
        ];

        $actualByCategory = [];
        foreach ($actualRows as $type => $row) {
            $cat = ($typeToCategory[$type] ?? BudgetCategory::Other)->value;
            $actualByCategory[$cat] = ($actualByCategory[$cat] ?? 0) + (float) $row->total;
        }

        $result = [];
        foreach (BudgetCategory::cases() as $cat) {
            $entry    = $budgets->get($cat->value);
            $result[] = [
                'category'         => $cat->value,
                'category_label'   => $cat->label(),
                'budgeted_amount'  => $entry ? (float) $entry->budgeted_amount : 0,
                'actual_collected' => round($actualByCategory[$cat->value] ?? 0, 2),
                'variance'         => round(($entry ? (float) $entry->budgeted_amount : 0) - ($actualByCategory[$cat->value] ?? 0), 2),
                'budget_uuid'      => $entry?->uuid,
            ];
        }

        return [
            'fiscal_year' => $year,
            'categories'  => $result,
            'totals'      => [
                'budgeted'  => round(collect($result)->sum('budgeted_amount'), 2),
                'actual'    => round(collect($result)->sum('actual_collected'), 2),
                'variance'  => round(collect($result)->sum('variance'), 2),
            ],
        ];
    }
}
