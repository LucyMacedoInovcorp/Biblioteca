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
    public $avaliacao;
    public $livro;

    public function __construct($status, $justification = null, $avaliacao = null)
    {
        $this->status = $status;
        $this->justification = $justification;
        $this->avaliacao = $avaliacao;
        $this->livro = $avaliacao ? \App\Models\Livro::find($avaliacao->livro_id) : null;
    }

    public function build()
    {
        return $this->subject('Resultado da sua Avaliação')
            ->view('emails.reviews.reviewresult')
            ->with([
                'status' => $this->status,
                'justification' => $this->justification,
                'avaliacao' => $this->avaliacao,
                'livro' => $this->livro,
            ]);
    }
}
