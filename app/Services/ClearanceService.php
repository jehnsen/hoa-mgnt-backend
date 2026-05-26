<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ClearancePurpose;
use App\Enums\ClearanceStatus;
use App\Models\Clearance;
use App\Models\User;
use App\Repositories\Contracts\ClearanceRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Services\Contracts\ClearanceServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ClearanceService implements ClearanceServiceInterface
{
    public function __construct(
        private readonly ClearanceRepositoryInterface $clearanceRepository,
        private readonly PropertyRepositoryInterface  $propertyRepository,
    ) {}

    public function list(User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        // Residents can only see their own clearance requests
        $requester = $viewer->isResident() ? $viewer : null;

        return $this->clearanceRepository->paginate(null, $requester, null, $perPage);
    }

    public function findOrFail(string $uuid): Clearance
    {
        return $this->clearanceRepository->findByUuid($uuid)
            ?? throw new NotFoundHttpException("Clearance [{$uuid}] not found.");
    }

    public function create(array $data, User $requester): Clearance
    {
        $property = $this->propertyRepository->findByUuid($data['property_id'])
            ?? throw new NotFoundHttpException("Property [{$data['property_id']}] not found.");

        return DB::transaction(fn () => $this->clearanceRepository->create([
            'property_id'  => $property->id,
            'requested_by' => $requester->id,
            'purpose'      => ClearancePurpose::from($data['purpose']),
            'notes'        => $data['notes'] ?? null,
            'status'       => ClearanceStatus::Pending,
        ]));
    }

    public function updateStatus(Clearance $clearance, ClearanceStatus $newStatus, array $data, User $actor): Clearance
    {
        if (! $clearance->canTransitionTo($newStatus)) {
            throw new HttpException(422, "Cannot transition from [{$clearance->status->value}] to [{$newStatus->value}].");
        }

        $updates = ['status' => $newStatus];

        if ($newStatus === ClearanceStatus::Issued) {
            if (empty($data['valid_until'])) {
                throw new HttpException(422, 'valid_until is required when issuing a clearance.');
            }

            $updates['issued_by']    = $actor->id;
            $updates['issued_at']    = now();
            $updates['valid_until']  = $data['valid_until'];
        }

        if ($newStatus === ClearanceStatus::Rejected) {
            if (empty($data['rejection_reason'])) {
                throw new HttpException(422, 'rejection_reason is required when rejecting a clearance.');
            }

            $updates['rejected_at']       = now();
            $updates['rejection_reason']  = $data['rejection_reason'];
        }

        return DB::transaction(fn () => $this->clearanceRepository->update($clearance, $updates));
    }
}
