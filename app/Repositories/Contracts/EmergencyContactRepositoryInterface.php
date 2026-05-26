<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmergencyContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface EmergencyContactRepositoryInterface
{
    public function findByUuid(string $uuid): ?EmergencyContact;

    public function allForUser(User $user): Collection;

    public function create(array $data): EmergencyContact;

    public function update(EmergencyContact $contact, array $data): EmergencyContact;

    public function delete(EmergencyContact $contact): void;
}
