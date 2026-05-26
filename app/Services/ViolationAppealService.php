<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ViolationStatus;
use App\Events\ViolationStatusUpdated;
use App\Exceptions\InvalidViolationTransitionException;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationAppeal;
use App\Repositories\Contracts\ViolationAppealRepositoryInterface;
use App\Repositories\Contracts\ViolationRepositoryInterface;
use App\Services\Contracts\ViolationAppealServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ViolationAppealService implements ViolationAppealServiceInterface
{
    public function __construct(
        private readonly ViolationAppealRepositoryInterface $appealRepository,
        private readonly ViolationRepositoryInterface       $violationRepository,
        private readonly AuditLogger                        $auditLogger,
    ) {}

    public function forViolation(Violation $violation): Collection
    {
        return $this->appealRepository->forViolation($violation);
    }

    public function findOrFail(string $uuid): ViolationAppeal
    {
        $appeal = $this->appealRepository->findByUuid($uuid);

        if ($appeal === null) {
            throw new NotFoundHttpException("Appeal [{$uuid}] not found.");
        }

        return $appeal;
    }

    public function create(Violation $violation, array $data, User $appellant): ViolationAppeal
    {
        if (! $violation->canTransitionTo(ViolationStatus::Appealed)) {
            throw new InvalidViolationTransitionException($violation->status, ViolationStatus::Appealed);
        }

        $previousStatus = $violation->status;

        [$appeal, $updated] = DB::transaction(function () use ($violation, $data, $appellant): array {
            $appeal = $this->appealRepository->create([
                'violation_id'    => $violation->id,
                'appellant_id'    => $appellant->id,
                'notes'           => $data['notes'],
                'evidence_images' => $data['evidence_images'] ?? null,
            ]);

            $updated = $this->violationRepository->updateStatus($violation, ViolationStatus::Appealed);

            return [$appeal, $updated];
        });

        ViolationStatusUpdated::dispatch($updated, $previousStatus, ViolationStatus::Appealed);

        $this->auditLogger->log(
            'violation',
            $updated->uuid,
            'appealed',
            ['status' => $previousStatus->value],
            ['status' => ViolationStatus::Appealed->value, 'appeal_id' => $appeal->uuid],
        );

        return $appeal->load(['violation', 'appellant']);
    }
}
