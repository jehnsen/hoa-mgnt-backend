<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ViolationStatus;
use RuntimeException;

final class InvalidViolationTransitionException extends RuntimeException
{
    public function __construct(ViolationStatus $from, ViolationStatus $to)
    {
        parent::__construct(
            "Invalid violation status transition from [{$from->value}] to [{$to->value}]."
        );
    }
}
