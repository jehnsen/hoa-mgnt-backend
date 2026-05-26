<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ReportingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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

    private function ensureBoardOrAdmin(): void
    {
        $user = request()->user();

        if (! ($user?->isSuperAdmin() || $user?->isBoardMember())) {
            abort(Response::HTTP_FORBIDDEN, 'Only board members or admins can access reports.');
        }
    }

    private function successResponse(mixed $data, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }
}
