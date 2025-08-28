<?php
namespace App\Mail;
use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class NovaRequisicaoMail extends Mailable
{
    use Queueable, SerializesModels;
public $requisicao;
public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }
public function build()
    {
        return $this->subject('📚 Nova Requisição de Livro')
                    ->markdown('emails.requisicoes.nova');
    }
}
