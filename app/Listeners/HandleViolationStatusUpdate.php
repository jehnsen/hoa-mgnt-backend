<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\ViolationStatus;
use App\Events\ViolationStatusUpdated;
use App\Notifications\ViolationIssuedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Reacts to violation status transitions.
 *
 * Implementing ShouldQueue offloads notification delivery to the queue worker
 * so the HTTP response returns immediately after the DB commit.
 */
class HandleViolationStatusUpdate implements ShouldQueue
{
    public string $queue = 'violations';

    public function handle(ViolationStatusUpdated $event): void
    {
        Log::channel('violations')->info('Violation status updated', [
            'violation_uuid'  => $event->violation->uuid,
            'property_unit'   => $event->violation->property->unit_number ?? 'N/A',
            'previous_status' => $event->previousStatus->value,
            'new_status'      => $event->newStatus->value,
        ]);

        // When a violation is formally issued, notify all residents of the property.
        if ($event->newStatus === ViolationStatus::Issued) {
            $notification = new ViolationIssuedNotification($event->violation);

            foreach ($event->violation->property->residents as $resident) {
                $resident->notify($notification);
            }
        }

        // When fully resolved, optionally trigger a ledger credit event.
        if ($event->newStatus === ViolationStatus::Resolved) {
            // TODO: dispatch ViolationResolvedAuditJob::class
        }
    }
}
