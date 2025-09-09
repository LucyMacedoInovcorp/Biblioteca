<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $justification;

    public function __construct($status, $justification = null)
    {
        $this->status = $status;
        $this->justification = $justification;
    }

    public function build()
    {
        return $this->subject('Resultado da sua Avaliação')
            ->view('emails.reviews.reviewresult');
    }
}
