<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteRequestApprovedNotification extends Notification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Delete Request Approved')
            ->line('Your delete request has been approved. Your data will be deleted shortly.');
    }
}
