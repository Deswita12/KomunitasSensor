<?php

namespace App\Mail;

use App\Models\HelpRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HelpRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public HelpRequest $helpRequest) {}

    public function build()
    {
        return $this->subject('Pesan Bantuan Baru — SensorKita')
            ->view('emails.help-request');
    }
}