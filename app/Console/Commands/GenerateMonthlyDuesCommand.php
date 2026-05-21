<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\BillingServiceInterface;
use Illuminate\Console\Command;

class GenerateMonthlyDuesCommand extends Command
{
    protected $signature   = 'hoa:generate-monthly-dues {period : Billing period in Y-m format, e.g. 2026-06}';
    protected $description = 'Generate monthly dues invoices for all active properties for the given period';

    public function __construct(
        private readonly BillingServiceInterface $billingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $period = (string) $this->argument('period');

        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            $this->error("Invalid period format. Use Y-m (e.g. 2026-06).");

            return self::FAILURE;
        }

        $this->info("Generating monthly dues for period [{$period}]...");

        $result = $this->billingService->generateBulkMonthlyDues($period);

        $this->info("Done — {$result['created']} created, {$result['skipped']} skipped (already invoiced).");

        return self::SUCCESS;
    }
}
