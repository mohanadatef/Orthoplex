<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private $user) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $verifyUrl = url("/api/v1/email/verify/{$this->user->id}/" . sha1($this->user->email));

        return (new MailMessage)
            ->subject(__('messages.verify_email_subject'))
            ->line(__('messages.verify_email_body'))
            ->action(__('messages.verify_email_button'), $verifyUrl);
    }
}
