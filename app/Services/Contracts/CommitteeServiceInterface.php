<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommitteeServiceInterface
{
    /** @return LengthAwarePaginator<Committee> */
    public function list(int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Committee;

    /** @param array<string, mixed> $data */
    public function create(array $data): Committee;

    /** @param array<string, mixed> $data */
    public function update(Committee $committee, array $data): Committee;

    public function delete(Committee $committee): void;

    /**
     * Add a user to a committee. Throws 409 if already an active member.
     *
     * @param array<string, mixed> $data
     */
    public function addMember(Committee $committee, User $user, array $data): CommitteeMember;

    public function removeMember(Committee $committee, User $user): CommitteeMember;
}
