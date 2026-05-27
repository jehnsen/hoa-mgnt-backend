<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\DelinquencyServiceInterface;
use Illuminate\Console\Command;

class CheckDelinquencyCommand extends Command
{
    protected $signature   = 'hoa:check-delinquency';
    protected $description = 'Flag properties with 3+ consecutive overdue monthly-dues invoices as delinquent.';

    public function handle(DelinquencyServiceInterface $delinquencyService): int
    {
        $result = $delinquencyService->runEscalation();

        $this->info("Delinquency check complete.");
        $this->line("  Flagged: {$result['flagged']}");
        $this->line("  Cleared: {$result['cleared']}");

        return Command::SUCCESS;
    }
}
