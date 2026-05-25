<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\ViolationStatus;
use App\Services\Contracts\ReportingServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class ReportingService implements ReportingServiceInterface
{
    public function financialSummary(string $month): array
    {
        $periodStart = Carbon::parse($month)->startOfMonth();
        $periodEnd   = $periodStart->copy()->endOfMonth();

        $invoices = DB::table('invoices')
            ->whereNull('deleted_at')
            ->whereBetween('period_month', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $collected = $invoices->get(InvoiceStatus::Paid->value)?->total ?? 0;
        $pending   = $invoices->get(InvoiceStatus::Pending->value)?->total ?? 0;
        $overdue   = $invoices->get(InvoiceStatus::Overdue->value)?->total ?? 0;
        $allTotal  = $collected + $pending + $overdue;

        return [
            'period'           => $periodStart->format('Y-m'),
            'collected'        => round((float) $collected, 2),
            'pending'          => round((float) $pending, 2),
            'overdue'          => round((float) $overdue, 2),
            'total_billed'     => round((float) $allTotal, 2),
            'collection_rate'  => $allTotal > 0 ? round($collected / $allTotal * 100, 2) : 0,
            'invoice_counts'   => $invoices->map(fn ($row) => $row->count),
        ];
    }

    public function violationTrends(int $months): array
    {
        $from = now()->subMonths($months)->startOfMonth();

        $rows = DB::table('violations')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', $from)
            ->selectRaw("strftime('%Y-%m', created_at) as month, status, COUNT(*) as count")
            ->groupByRaw("month, status")
            ->orderBy('month')
            ->get();

        $grouped = $rows->groupBy('month')->map(fn ($items) => $items->keyBy('status')->map(fn ($r) => $r->count));

        $statuses = array_column(ViolationStatus::cases(), 'value');

        return [
            'months'   => $months,
            'from'     => $from->format('Y-m'),
            'trends'   => $grouped->map(fn ($byStat) => collect($statuses)->mapWithKeys(fn ($s) => [$s => $byStat[$s] ?? 0])),
        ];
    }

    public function occupancySummary(): array
    {
        $total  = DB::table('properties')->whereNull('deleted_at')->where('is_active', true)->count();
        $occupied = DB::table('property_user')
            ->whereNull('move_out_at')
            ->distinct('property_id')
            ->count('property_id');

        return [
            'total_units'    => $total,
            'occupied_units' => $occupied,
            'vacant_units'   => $total - $occupied,
            'occupancy_rate' => $total > 0 ? round($occupied / $total * 100, 2) : 0,
        ];
    }

    public function maintenanceSummary(): array
    {
        $rows = DB::table('maintenance_requests')
            ->whereNull('deleted_at')
            ->selectRaw('status, priority, COUNT(*) as count')
            ->groupBy('status', 'priority')
            ->get();

        $byStatus   = $rows->groupBy('status')->map(fn ($r) => $r->sum('count'));
        $byPriority = $rows->groupBy('priority')->map(fn ($r) => $r->sum('count'));

        $open = DB::table('maintenance_requests')
            ->whereNull('deleted_at')
            ->whereNotIn('status', [MaintenanceStatus::Resolved->value, MaintenanceStatus::Closed->value])
            ->count();

        return [
            'total_open'  => $open,
            'by_status'   => $byStatus,
            'by_priority' => $byPriority,
        ];
    }
}
