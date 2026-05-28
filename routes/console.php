<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Flag properties with 3+ consecutive overdue monthly dues as delinquent.
// Runs at 01:00 AM daily so the result is ready when staff arrive.
Schedule::command('hoa:check-delinquency')->dailyAt('01:00');

// Re-apply late fees at midnight so overdue invoices are updated each day.
Schedule::command('hoa:apply-late-fees')->dailyAt('00:05');
