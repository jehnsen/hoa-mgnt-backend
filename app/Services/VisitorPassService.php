<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\VisitorPass;
use App\Repositories\Contracts\VisitorPassRepositoryInterface;
use App\Services\AuditLogger;
use App\Services\Contracts\VisitorPassServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class VisitorPassService implements VisitorPassServiceInterface
{
    public function __construct(
        private readonly VisitorPassRepositoryInterface $passRepository,
        private readonly AuditLogger                    $auditLogger,
    ) {}

    public function forProperty(Property $property, bool $activeOnly = false): Collection
    {
        return $this->passRepository->forProperty($property->id, $activeOnly);
    }

    public function findOrFail(string $uuid): VisitorPass
    {
        $pass = $this->passRepository->findByUuid($uuid);

        if ($pass === null) {
            throw new NotFoundHttpException("Visitor pass [{$uuid}] not found.");
        }

        return $pass;
    }

    public function create(Property $property, User $resident, array $data): VisitorPass
    {
        return $this->passRepository->create([
            'property_id'  => $property->id,
            'resident_id'  => $resident->id,
            'visitor_name' => $data['visitor_name'],
            'vehicle_plate' => $data['vehicle_plate'] ?? null,
            'expected_at'  => $data['expected_at'],
            'expires_at'   => $data['expires_at'],
            'purpose'      => $data['purpose'] ?? null,
            'access_code'  => strtoupper(Str::random(6)),
            'is_used'      => false,
        ]);
    }

    public function checkIn(string $accessCode): VisitorPass
    {
        $pass = $this->passRepository->findByAccessCode(strtoupper($accessCode));

        if ($pass === null) {
            throw new NotFoundHttpException("Visitor pass with code [{$accessCode}] not found.");
        }

        if ($pass->is_used) {
            throw new HttpException(409, 'This visitor pass has already been used.');
        }

        if ($pass->isExpired()) {
            throw new HttpException(422, 'This visitor pass has expired.');
        }

        $updated = $this->passRepository->update($pass, [
            'is_used' => true,
            'used_at' => now(),
        ]);

        $this->auditLogger->log(
            'visitor_pass',
            $updated->uuid,
            'visitor_pass_checked_in',
            null,
            ['visitor_name' => $updated->visitor_name, 'used_at' => $updated->used_at->toIso8601String()],
        );

        return $updated;
    }

    public function delete(VisitorPass $pass): void
    {
        $this->passRepository->delete($pass);
    }
}
