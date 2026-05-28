<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

final class AuditLogger
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogRepository,
    ) {}

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

    /** @return LengthAwarePaginator<AuditLog> */
    public function list(
        ?string $type    = null,
        ?string $uuid    = null,
        ?string $action  = null,
        int     $perPage = 20,
    ): LengthAwarePaginator {
        return $this->auditLogRepository->paginate($type, $uuid, $action, $perPage);
    }

    /** @return LengthAwarePaginator<AuditLog> */
    public function forEntity(string $type, string $uuid, int $perPage = 50): LengthAwarePaginator
    {
        return $this->auditLogRepository->paginateForEntity($type, $uuid, $perPage);
    }
}
