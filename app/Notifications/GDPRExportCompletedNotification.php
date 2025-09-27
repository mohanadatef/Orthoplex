<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GDPRExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $downloadUrl) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.gdpr_export_subject'))
            ->line(__('messages.gdpr_export_body'))
            ->action(__('messages.gdpr_export_button'), $this->downloadUrl);
    }
}
