<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $url) {}

    public function build()
    {
        return $this->subject('Verify your email')->view('emails.verify', ['url'=>$this->url]);
    }
}
