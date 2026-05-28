<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuditLogRepositoryInterface
{
    /** @return LengthAwarePaginator<\App\Models\AuditLog> */
    public function paginate(
        ?string $type   = null,
        ?string $uuid   = null,
        ?string $action = null,
        int     $perPage = 20,
    ): LengthAwarePaginator;

    /** @return LengthAwarePaginator<\App\Models\AuditLog> */
    public function paginateForEntity(string $type, string $uuid, int $perPage = 50): LengthAwarePaginator;
}
