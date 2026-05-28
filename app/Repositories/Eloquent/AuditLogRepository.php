<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function paginate(
        ?string $type    = null,
        ?string $uuid    = null,
        ?string $action  = null,
        int     $perPage = 20,
    ): LengthAwarePaginator {
        return AuditLog::with('actor')
            ->when($type,   fn ($q) => $q->where('auditable_type', $type))
            ->when($uuid,   fn ($q) => $q->where('auditable_uuid', $uuid))
            ->when($action, fn ($q) => $q->where('action', $action))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function paginateForEntity(string $type, string $uuid, int $perPage = 50): LengthAwarePaginator
    {
        return AuditLog::with('actor')
            ->where('auditable_type', $type)
            ->where('auditable_uuid', $uuid)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
