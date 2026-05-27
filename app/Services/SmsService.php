<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SmsService implements SmsServiceInterface
{
    public function send(string $phone, string $message): void
    {
        if (config('sms.driver') === 'log') {
            Log::channel('stack')->info('[SMS]', ['to' => $phone, 'message' => $message]);
            return;
        }

        Http::post(config('sms.endpoint'), [
            'apikey'     => config('sms.api_key'),
            'number'     => $phone,
            'message'    => $message,
            'sendername' => config('sms.sender_name'),
        ]);
    }

    public function broadcast(iterable $phones, string $message): void
    {
        foreach ($phones as $phone) {
            if ($phone) {
                $this->send((string) $phone, $message);
            }
        }
    }
}
