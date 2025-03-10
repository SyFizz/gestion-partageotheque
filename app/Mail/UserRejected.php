<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Demande d\'inscription refusée')
            ->markdown('emails.users.rejected');
    }
}
