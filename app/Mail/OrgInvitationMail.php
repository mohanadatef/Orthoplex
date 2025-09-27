<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrgInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $rawToken) {}

    public function build()
    {
        $url = url('/accept-invite?token='.$this->rawToken);
        return $this->subject('Your invitation')->view('emails.invitation', ['url'=>$url]);
    }
}
