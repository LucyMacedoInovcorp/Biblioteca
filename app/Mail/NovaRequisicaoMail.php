<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NovaRequisicaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $requisicao;
    public $imagemPath; 

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
        // Verifica se o livro tem imagem
        if ($this->requisicao->livro->imagemcapa) {
            $caminho = public_path($this->requisicao->livro->imagemcapa);

            // Adiciona a imagem como anexo inline
            $this->attach($caminho, [
                'as' => 'capa.jpg',
                'mime' => 'image/jpeg',
                'display' => 'inline', 
            ]);

           
            $this->imagemPath = 'cid:capa.jpg';
        }

        return $this->subject('📚 Nova Requisição de Livro')
                    ->markdown('emails.nova_requisicao')
                    ->with([
                        'imagemPath' => $this->imagemPath ?? null,
                    ]);
    }
}
