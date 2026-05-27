<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\VisitorPass;
use Illuminate\Database\Eloquent\Collection;

interface VisitorPassRepositoryInterface
{
    public function findByUuid(string $uuid): ?VisitorPass;

    public function findByAccessCode(string $code): ?VisitorPass;

    /** @return Collection<int, VisitorPass> */
    public function forProperty(int $propertyId, bool $activeOnly = false): Collection;

    /** @param array<string, mixed> $data */
    public function create(array $data): VisitorPass;

    /** @param array<string, mixed> $data */
    public function update(VisitorPass $pass, array $data): VisitorPass;

    public function delete(VisitorPass $pass): void;
}
