<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface ReportingServiceInterface
{
    /** @return array<string, mixed> */
    public function financialSummary(string $month): array;

    /** @return array<string, mixed> */
    public function violationTrends(int $months): array;

    /** @return array<string, mixed> */
    public function occupancySummary(): array;

    /** @return array<string, mixed> */
    public function maintenanceSummary(): array;
}
