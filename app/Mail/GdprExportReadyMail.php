<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GdprExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $downloadPath) {}

    public function build()
    {
        return $this->subject('Your GDPR Export is ready')
            ->view('emails.gdpr_export', ['path'=>$this->downloadPath]);
    }
}
