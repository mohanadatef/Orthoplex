<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GdprExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $url) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your GDPR Export is Ready')
            ->line('You requested a copy of your personal data.')
            ->action('Download Data', $this->url)
            ->line('This link will expire after 24 hours and can be used once.');
    }
}
