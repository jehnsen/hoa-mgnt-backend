<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentCategory: string
{
    case Rules      = 'rules';
    case Minutes    = 'minutes';
    case Financials = 'financials';
    case Forms      = 'forms';
    case Notices    = 'notices';
    case Other      = 'other';

    public function label(): string
    {
        return match($this) {
            self::Rules      => 'HOA Rules & Bylaws',
            self::Minutes    => 'Meeting Minutes',
            self::Financials => 'Financial Documents',
            self::Forms      => 'Forms & Applications',
            self::Notices    => 'Notices',
            self::Other      => 'Other',
        };
    }
}
