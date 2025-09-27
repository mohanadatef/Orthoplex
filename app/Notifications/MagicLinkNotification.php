<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MagicLinkNotification extends Notification implements ShouldQueue
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
            ->subject(__('messages.magic_link_subject'))
            ->line(__('messages.magic_link_body'))
            ->action(__('messages.magic_link_button'), $this->url);
    }
}
