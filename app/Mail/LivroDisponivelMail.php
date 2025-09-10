<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class LivroDisponivelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $livro;

    public function __construct($livro)
    {
        $this->livro = $livro;
    }

    public function build()
    {
        return $this->subject('Livro disponível para requisição')
            ->markdown('emails.livro_disponivel.livro_disponivel')
            ->with([
                'livro' => $this->livro,
            ]);
    }
}


