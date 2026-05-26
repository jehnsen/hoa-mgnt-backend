<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\User;
use App\Repositories\Contracts\CommitteeRepositoryInterface;
use App\Services\Contracts\CommitteeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CommitteeService implements CommitteeServiceInterface
{
    public function __construct(
        private readonly CommitteeRepositoryInterface $committeeRepository,
    ) {}

    public function list(int $perPage = 20): LengthAwarePaginator
    {
        return $this->committeeRepository->paginate($perPage);
    }

    public function findOrFail(string $uuid): Committee
    {
        $committee = $this->committeeRepository->findByUuid($uuid);

        if ($committee === null) {
            throw new NotFoundHttpException("Committee [{$uuid}] not found.");
        }

        return $committee;
    }

    public function create(array $data): Committee
    {
        return DB::transaction(function () use ($data): Committee {
            return $this->committeeRepository->create([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active'   => $data['is_active'] ?? true,
            ]);
        });
    }

    public function update(Committee $committee, array $data): Committee
    {
        return $this->committeeRepository->update($committee, $data);
    }

    public function delete(Committee $committee): void
    {
        $this->committeeRepository->delete($committee);
    }

    public function addMember(Committee $committee, User $user, array $data): CommitteeMember
    {
        $existing = $this->committeeRepository->findActiveMember($committee, $user);

        if ($existing !== null) {
            throw new HttpException(409, 'User is already an active member of this committee.');
        }

        return DB::transaction(function () use ($committee, $user, $data): CommitteeMember {
            return $this->committeeRepository->addMember([
                'committee_id' => $committee->id,
                'user_id'      => $user->id,
                'role'         => $data['role'] ?? 'member',
                'joined_at'    => $data['joined_at'] ?? now()->toDateString(),
            ]);
        });
    }

    public function removeMember(Committee $committee, User $user): CommitteeMember
    {
        $member = $this->committeeRepository->findActiveMember($committee, $user);

        if ($member === null) {
            throw new NotFoundHttpException('User is not an active member of this committee.');
        }

        return $this->committeeRepository->removeMember($member);
    }
}
