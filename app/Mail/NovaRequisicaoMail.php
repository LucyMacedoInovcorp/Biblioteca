<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NovaRequisicaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requisicao;

    /**
     * Create a new message instance.
     */
    public function __construct($requisicao)
    {
        $this->requisicao = $requisicao;
    }

    /**
     * Build the message.
     */
public function build()
{
    return $this->subject('📚 Nova Requisição de Livro')
                ->bcc('admin@exemplo.com')
                ->markdown('emails.nova_requisicao');
}

}
