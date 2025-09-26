<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteRequestRejectedNotification extends Notification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Delete Request Rejected')
            ->line('Your delete request has been rejected by the administrator.');
    }
}
