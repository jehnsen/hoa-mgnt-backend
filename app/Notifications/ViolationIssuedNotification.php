<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Violation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ViolationIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Violation $violation,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("HOA Violation Notice – {$this->violation->title}")
            ->greeting("Dear {$notifiable->name},")
            ->line("A violation has been issued for property **{$this->violation->property->unit_number}**.")
            ->line("**Violation:** {$this->violation->title}")
            ->line("**Description:** {$this->violation->description}")
            ->line("**Fine Amount:** ₱" . number_format((float) $this->violation->fine_amount, 2))
            ->action('View Violation Details', url("/violations/{$this->violation->uuid}"))
            ->line('If you believe this violation was issued in error, you may appeal within 15 days.')
            ->salutation('Springdale HOA Management');
    }

    /** @return array<string, mixed> */
    public function toArray(mixed $notifiable): array
    {
        return [
            'violation_uuid' => $this->violation->uuid,
            'title'          => $this->violation->title,
            'fine_amount'    => $this->violation->fine_amount,
            'property_id'    => $this->violation->property_id,
        ];
    }
}
