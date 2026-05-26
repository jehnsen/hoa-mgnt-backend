<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommitteeRepositoryInterface
{
    public function findByUuid(string $uuid): ?Committee;

    /** @return LengthAwarePaginator<Committee> */
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    /** @param array<string, mixed> $data */
    public function create(array $data): Committee;

    /** @param array<string, mixed> $data */
    public function update(Committee $committee, array $data): Committee;

    public function delete(Committee $committee): void;

    public function findActiveMember(Committee $committee, User $user): ?CommitteeMember;

    /** @param array<string, mixed> $data */
    public function addMember(array $data): CommitteeMember;

    public function removeMember(CommitteeMember $member): CommitteeMember;
}
