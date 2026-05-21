<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Enums\ViolationStatus;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ViolationServiceInterface
{
    /** @return LengthAwarePaginator<Violation> */
    public function list(?ViolationStatus $status = null, ?int $propertyId = null, int $perPage = 20): LengthAwarePaginator;

    public function findOrFail(string $uuid): Violation;

    /**
     * Log a new violation report, optionally attaching geotagged evidence images.
     *
     * @param array<string, mixed>                         $data
     * @param array<int, array{path:string, lat:float, lng:float, captured_at:string}> $evidenceImages
     */
    public function create(array $data, User $reporter, array $evidenceImages = []): Violation;

    /**
     * Transition the violation through its state machine.
     * Fires ViolationStatusUpdated event on success.
     *
     * @throws \App\Exceptions\InvalidViolationTransitionException
     */
    public function updateStatus(string $uuid, ViolationStatus $newStatus): Violation;

    /**
     * Append geotagged images to an existing violation's evidence set.
     *
     * @param array<int, array{path:string, lat:float, lng:float, captured_at:string}> $images
     */
    public function appendEvidence(string $uuid, array $images): Violation;
}
