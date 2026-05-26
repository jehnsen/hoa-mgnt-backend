<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\EmergencyContact;
use App\Models\User;
use App\Repositories\Contracts\EmergencyContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EmergencyContactRepository implements EmergencyContactRepositoryInterface
{
    public function __construct(private readonly EmergencyContact $model) {}

    public function findByUuid(string $uuid): ?EmergencyContact
    {
        return $this->model->newQuery()
                           ->where('uuid', $uuid)
                           ->with('user')
                           ->first();
    }

    public function allForUser(User $user): Collection
    {
        return $this->model->newQuery()
                           ->where('user_id', $user->id)
                           ->orderByDesc('is_primary')
                           ->orderBy('name')
                           ->get();
    }

    public function create(array $data): EmergencyContact
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(EmergencyContact $contact, array $data): EmergencyContact
    {
        $contact->fill($data)->save();

        return $contact->refresh();
    }

    public function delete(EmergencyContact $contact): void
    {
        $contact->delete();
    }
}
