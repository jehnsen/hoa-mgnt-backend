<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\AmenityBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly AmenityBooking $booking,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject("Amenity Booking Received – {$this->booking->amenity->name}")
            ->greeting("Dear {$notifiable->name},")
            ->line("Your amenity booking request has been submitted and is pending approval.")
            ->line("**Amenity:** {$this->booking->amenity->name}")
            ->line("**Date & Time:** {$this->booking->start_at->format('F j, Y g:i A')} – {$this->booking->end_at->format('g:i A')}")
            ->line("**Property:** {$this->booking->property->unit_number}")
            ->line("**Reference No.:** {$this->booking->uuid}");

        if ($this->booking->invoice !== null) {
            $message->line("**Booking Fee Invoice:** ₱" . number_format((float) $this->booking->invoice->total_amount, 2) . " — please settle before the booking date.");
        }

        return $message
            ->line('You will receive another notification once your booking is approved or if further action is required.')
            ->action('View Booking Details', url("/amenities/bookings/{$this->booking->uuid}"))
            ->salutation('Springdale HOA Management');
    }

    /** @return array<string, mixed> */
    public function toArray(mixed $notifiable): array
    {
        return [
            'booking_uuid' => $this->booking->uuid,
            'amenity_name' => $this->booking->amenity->name,
            'start_at'     => $this->booking->start_at->toIso8601String(),
            'end_at'       => $this->booking->end_at->toIso8601String(),
            'property_id'  => $this->booking->property_id,
        ];
    }
}
