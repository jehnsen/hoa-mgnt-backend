<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\BillingServiceInterface;
use Illuminate\Console\Command;

class ApplyLateFeesCommand extends Command
{
    protected $signature   = 'hoa:apply-late-fees';
    protected $description = 'Mark all past-due pending invoices as Overdue and apply the late-fee surcharge';

    public function __construct(
        private readonly BillingServiceInterface $billingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Scanning for overdue invoices...');

        $count = $this->billingService->applyLateFees();

        $this->info("{$count} invoice(s) marked Overdue with late fees applied.");

        return self::SUCCESS;
    }
}
