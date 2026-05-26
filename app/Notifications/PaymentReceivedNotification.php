<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Invoice $invoice,
        private readonly Payment $payment,
        private readonly float   $remainingBalance,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject("Payment Received – {$this->invoice->description}")
            ->greeting("Dear {$notifiable->name},")
            ->line("A payment of ₱" . number_format((float) $this->payment->amount, 2) . " has been received for property **{$this->invoice->property->unit_number}**.")
            ->line("**Invoice:** {$this->invoice->description}")
            ->line("**Payment Method:** {$this->payment->payment_method->label()}")
            ->line("**Amount Paid:** ₱" . number_format((float) $this->payment->amount, 2));

        if ($this->remainingBalance > 0.0) {
            $message->line("**Remaining Balance:** ₱" . number_format($this->remainingBalance, 2))
                    ->line('Please settle the remaining balance before the due date to avoid late fees.');
        } else {
            $message->line('Your invoice has been **fully paid**. Thank you!');
        }

        if ($this->payment->transaction_reference) {
            $message->line("**Reference No.:** {$this->payment->transaction_reference}");
        }

        return $message->action('View Invoice', url("/financials/invoices/{$this->invoice->uuid}"))
                       ->salutation('Springdale HOA Management');
    }

    /** @return array<string, mixed> */
    public function toArray(mixed $notifiable): array
    {
        return [
            'invoice_uuid'      => $this->invoice->uuid,
            'payment_uuid'      => $this->payment->uuid,
            'amount_paid'       => $this->payment->amount,
            'remaining_balance' => $this->remainingBalance,
            'property_id'       => $this->invoice->property_id,
        ];
    }
}
