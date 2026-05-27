<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface SmsServiceInterface
{
    public function send(string $phone, string $message): void;

    /** @param iterable<string> $phones */
    public function broadcast(iterable $phones, string $message): void;
}
