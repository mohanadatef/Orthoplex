<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $url) {}

    public function build()
    {
        return $this->subject('Your magic login link')->view('emails.magic_link', ['url'=>$this->url]);
    }
}
