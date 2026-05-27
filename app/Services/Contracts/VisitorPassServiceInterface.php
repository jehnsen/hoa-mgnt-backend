<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Property;
use App\Models\User;
use App\Models\VisitorPass;
use Illuminate\Database\Eloquent\Collection;

interface VisitorPassServiceInterface
{
    /** @return Collection<int, VisitorPass> */
    public function forProperty(Property $property, bool $activeOnly = false): Collection;

    public function findOrFail(string $uuid): VisitorPass;

    /** @param array<string, mixed> $data */
    public function create(Property $property, User $resident, array $data): VisitorPass;

    public function checkIn(string $accessCode): VisitorPass;

    public function delete(VisitorPass $pass): void;
}
