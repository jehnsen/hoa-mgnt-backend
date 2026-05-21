<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\ViolationStatus;
use App\Models\Violation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ViolationStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Violation       $violation,
        public readonly ViolationStatus $previousStatus,
        public readonly ViolationStatus $newStatus,
    ) {}
}
