<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ReportingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportController extends Controller
{
    public function __construct(
        private readonly ReportingServiceInterface $reportingService,
    ) {}

    public function dashboard(): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        return $this->successResponse(
            $this->reportingService->dashboardSummary(),
            'Dashboard summary'
        );
    }

    public function financial(): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        $month = request()->string('month', now()->format('Y-m'))->toString();

        return $this->successResponse(
            $this->reportingService->financialSummary($month),
            'Financial summary'
        );
    }

    public function violations(): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        $months = (int) request()->integer('months', 6);

        return $this->successResponse(
            $this->reportingService->violationTrends($months),
            'Violation trends'
        );
    }

    public function occupancy(): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        return $this->successResponse(
            $this->reportingService->occupancySummary(),
            'Occupancy summary'
        );
    }

    public function maintenance(): JsonResponse
    {
        $this->ensureBoardOrAdmin();

        return $this->successResponse(
            $this->reportingService->maintenanceSummary(),
            'Maintenance summary'
        );
    }

    public function export(): StreamedResponse
    {
        $this->ensureBoardOrAdmin();

        $type = request()->string('type', 'financial')->toString();

        return match($type) {
            'violations'  => $this->exportViolationsCsv(),
            'maintenance' => $this->exportMaintenanceCsv(),
            default       => $this->exportFinancialCsv(),
        };
    }

    private function exportFinancialCsv(): StreamedResponse
    {
        $month = request()->string('month', now()->format('Y-m'))->toString();
        $data  = $this->reportingService->financialSummary($month);

        $filename = "financial-{$month}.csv";

        return response()->streamDownload(function () use ($data): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Metric', 'Value']);
            fputcsv($out, ['Period', $data['period']]);
            fputcsv($out, ['Total Billed', $data['total_billed']]);
            fputcsv($out, ['Collected', $data['collected']]);
            fputcsv($out, ['Pending', $data['pending']]);
            fputcsv($out, ['Overdue', $data['overdue']]);
            fputcsv($out, ['Collection Rate (%)', $data['collection_rate']]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function exportViolationsCsv(): StreamedResponse
    {
        $months = (int) request()->integer('months', 6);
        $data   = $this->reportingService->violationTrends($months);

        return response()->streamDownload(function () use ($data): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Month', 'Status', 'Count']);
            foreach ($data['trends'] as $month => $byStatus) {
                foreach ($byStatus as $status => $count) {
                    fputcsv($out, [$month, $status, $count]);
                }
            }
            fclose($out);
        }, "violations-{$months}m.csv", ['Content-Type' => 'text/csv']);
    }

    private function exportMaintenanceCsv(): StreamedResponse
    {
        $rows = \Illuminate\Support\Facades\DB::table('maintenance_requests')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get(['uuid', 'title', 'category', 'priority', 'status', 'estimated_cost', 'actual_cost', 'created_at', 'resolved_at']);

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Title', 'Category', 'Priority', 'Status', 'Estimated Cost', 'Actual Cost', 'Created At', 'Resolved At']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->uuid, $row->title, $row->category, $row->priority, $row->status, $row->estimated_cost, $row->actual_cost, $row->created_at, $row->resolved_at]);
            }
            fclose($out);
        }, 'maintenance.csv', ['Content-Type' => 'text/csv']);
    }

    private function ensureBoardOrAdmin(): void
    {
        $user = request()->user();

        if (! ($user?->isSuperAdmin() || $user?->isBoardMember())) {
            abort(Response::HTTP_FORBIDDEN, 'Only board members or admins can access reports.');
        }
    }

}
