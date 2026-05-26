<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Violation;
use App\Models\ViolationAppeal;
use Illuminate\Database\Eloquent\Collection;

interface ViolationAppealRepositoryInterface
{
    public function findByUuid(string $uuid): ?ViolationAppeal;

    /** @return Collection<int, ViolationAppeal> */
    public function forViolation(Violation $violation): Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): ViolationAppeal;
}
