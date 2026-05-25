<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->isSuperAdmin() || $user->isBoardMember()) {
            return true;
        }

        return $document->is_public && $document->published_at !== null;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function update(User $user, Document $document): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->isSuperAdmin() || $user->isBoardMember();
    }
}
