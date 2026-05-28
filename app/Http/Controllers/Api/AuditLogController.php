<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Services\AuditLogger;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->ensureAdmin();

        $logs = $this->auditLogger->list(
            type:    request('type'),
            uuid:    request('uuid'),
            action:  request('action'),
            perPage: $this->perPage(),
        );

        return AuditLogResource::collection($logs);
    }

    public function forEntity(string $type, string $uuid): AnonymousResourceCollection
    {
        $this->ensureAdmin();

        return AuditLogResource::collection(
            $this->auditLogger->forEntity($type, $uuid)
        );
    }

    private function ensureAdmin(): void
    {
        if (! request()->user()?->isSuperAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'Only super-admins can view audit logs.');
        }
    }
}
