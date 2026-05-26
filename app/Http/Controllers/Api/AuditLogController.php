<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AuditLogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->ensureAdmin();

        $logs = AuditLog::with('actor')
            ->when(request('type'), fn ($q) => $q->where('auditable_type', request('type')))
            ->when(request('uuid'), fn ($q) => $q->where('auditable_uuid', request('uuid')))
            ->when(request('action'), fn ($q) => $q->where('action', request('action')))
            ->orderByDesc('created_at')
            ->paginate((int) request()->integer('per_page', 20));

        return AuditLogResource::collection($logs);
    }

    public function forEntity(string $type, string $uuid): AnonymousResourceCollection
    {
        $this->ensureAdmin();

        $logs = AuditLog::with('actor')
            ->where('auditable_type', $type)
            ->where('auditable_uuid', $uuid)
            ->orderByDesc('created_at')
            ->paginate(50);

        return AuditLogResource::collection($logs);
    }

    private function ensureAdmin(): void
    {
        if (! request()->user()?->isSuperAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Only super-admins can view audit logs.');
        }
    }
}
