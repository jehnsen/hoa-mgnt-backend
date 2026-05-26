<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

final class AuditLogger
{
    /**
     * Record an auditable action against a domain entity.
     *
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function log(
        string  $auditableType,
        string  $auditableUuid,
        string  $action,
        ?array  $oldValues = null,
        ?array  $newValues = null,
    ): void {
        AuditLog::create([
            'auditable_type' => $auditableType,
            'auditable_uuid' => $auditableUuid,
            'action'         => $action,
            'actor_id'       => Auth::id(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => Request::ip(),
        ]);
    }
}
