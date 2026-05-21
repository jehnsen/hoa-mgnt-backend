<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;

class InvoicePolicy
{
    /** Board members and SuperAdmins can list any property's invoices. Residents can only see their own. */
    public function viewAny(User $user, Property $property): bool
    {
        if ($user->canManageFinancials()) {
            return true;
        }

        return $user->properties()->where('properties.id', $property->id)->exists();
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->canManageFinancials()) {
            return true;
        }

        return $user->properties()->where('properties.id', (int) $invoice->property_id)->exists();
    }

    public function processPayment(User $user): bool
    {
        return $user->canManageFinancials();
    }

    public function cancel(User $user): bool
    {
        return $user->canManageFinancials();
    }
}
