<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\EmergencyContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface EmergencyContactServiceInterface
{
    public function listForUser(string $userUuid, User $viewer): Collection;

    public function findOrFail(string $uuid): EmergencyContact;

    public function create(string $userUuid, array $data, User $actor): EmergencyContact;

    public function update(EmergencyContact $contact, array $data): EmergencyContact;

    public function delete(EmergencyContact $contact): void;
}
