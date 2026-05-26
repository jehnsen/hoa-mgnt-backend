<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\User;
use App\Repositories\Contracts\CommitteeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class CommitteeRepository implements CommitteeRepositoryInterface
{
    public function __construct(
        private readonly Committee $model,
        private readonly CommitteeMember $memberModel,
    ) {}

    public function findByUuid(string $uuid): ?Committee
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with(['activeMembers.user'])
                           ->first();
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
                           ->withCount('activeMembers')
                           ->orderBy('name')
                           ->paginate($perPage);
    }

    public function create(array $data): Committee
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Committee $committee, array $data): Committee
    {
        $committee->fill($data)->save();

        return $committee->refresh();
    }

    public function delete(Committee $committee): void
    {
        $committee->delete();
    }

    public function findActiveMember(Committee $committee, User $user): ?CommitteeMember
    {
        return $this->memberModel->newQuery()
                                 ->where('committee_id', $committee->id)
                                 ->where('user_id', $user->id)
                                 ->whereNull('left_at')
                                 ->first();
    }

    public function addMember(array $data): CommitteeMember
    {
        return $this->memberModel->newQuery()->create($data);
    }

    public function removeMember(CommitteeMember $member): CommitteeMember
    {
        $member->left_at = Carbon::today();
        $member->save();

        return $member->refresh();
    }
}
