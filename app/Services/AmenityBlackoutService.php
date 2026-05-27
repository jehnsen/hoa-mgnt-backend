<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Amenity;
use App\Models\AmenityBlackout;
use App\Repositories\Contracts\AmenityBlackoutRepositoryInterface;
use App\Services\Contracts\AmenityBlackoutServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AmenityBlackoutService implements AmenityBlackoutServiceInterface
{
    public function __construct(
        private readonly AmenityBlackoutRepositoryInterface $blackoutRepository,
    ) {}

    public function forAmenity(Amenity $amenity): Collection
    {
        return $this->blackoutRepository->forAmenity($amenity);
    }

    public function findOrFail(string $uuid): AmenityBlackout
    {
        $blackout = $this->blackoutRepository->findByUuid($uuid);

        if ($blackout === null) {
            throw new NotFoundHttpException("Blackout window [{$uuid}] not found.");
        }

        return $blackout;
    }

    public function create(Amenity $amenity, array $data): AmenityBlackout
    {
        return $this->blackoutRepository->create([
            'amenity_id' => $amenity->id,
            'start_at'   => $data['start_at'],
            'end_at'     => $data['end_at'],
            'reason'     => $data['reason'] ?? null,
        ]);
    }

    public function delete(AmenityBlackout $blackout): void
    {
        $this->blackoutRepository->delete($blackout);
    }
}
