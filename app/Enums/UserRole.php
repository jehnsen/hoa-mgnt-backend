<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin  = 'super_admin';
    case BoardMember = 'board_member';
    case Resident    = 'resident';
    case Vendor      = 'vendor';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin  => 'Super Administrator',
            self::BoardMember => 'Board Member',
            self::Resident    => 'Resident',
            self::Vendor      => 'Vendor',
        };
    }

    /** Roles permitted to manage financial records */
    public function canManageFinancials(): bool
    {
        return in_array($this, [self::SuperAdmin, self::BoardMember], strict: true);
    }

    /** Roles permitted to issue and update violations */
    public function canManageViolations(): bool
    {
        return in_array($this, [self::SuperAdmin, self::BoardMember], strict: true);
    }
}
