<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MagicLinkNotification extends Notification
{
    protected $url;
    public function __construct(string $url) { $this->url = $url; }

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Magic Login Link')
            ->line('Click the button below to login:')
            ->action('Login', $this->url)
            ->line('This link will expire soon and can only be used once.');
    }
}
