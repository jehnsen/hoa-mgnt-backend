<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MaintenanceRequest $request,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("Maintenance Request Received – {$this->request->title}")
            ->greeting("Dear {$notifiable->name},")
            ->line("Your maintenance request has been received and is now under review.")
            ->line("**Request:** {$this->request->title}")
            ->line("**Category:** {$this->request->category->label()}")
            ->line("**Priority:** {$this->request->priority->label()}")
            ->line("**Property:** {$this->request->property->unit_number}")
            ->line("**Reference No.:** {$this->request->uuid}")
            ->line('Our team will assess the request and get back to you shortly. Urgent and high-priority requests are typically addressed within 24 hours.')
            ->action('View Request Status', url("/maintenance/{$this->request->uuid}"))
            ->salutation('Springdale HOA Management');
    }

    /** @return array<string, mixed> */
    public function toArray(mixed $notifiable): array
    {
        return [
            'request_uuid' => $this->request->uuid,
            'title'        => $this->request->title,
            'priority'     => $this->request->priority->value,
            'property_id'  => $this->request->property_id,
        ];
    }
}
