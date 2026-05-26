<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationAppeal;
use Illuminate\Database\Eloquent\Collection;

interface ViolationAppealServiceInterface
{
    /** @return Collection<int, ViolationAppeal> */
    public function forViolation(Violation $violation): Collection;

    public function findOrFail(string $uuid): ViolationAppeal;

    /**
     * Create an appeal record and atomically transition the violation to 'appealed'.
     *
     * @param array<string, mixed> $data
     * @throws \App\Exceptions\InvalidViolationTransitionException
     */
    public function create(Violation $violation, array $data, User $appellant): ViolationAppeal;
}
