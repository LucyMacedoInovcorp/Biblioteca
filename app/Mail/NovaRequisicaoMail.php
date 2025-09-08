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
        $imagemcapa = $this->requisicao->livro->imagemcapa;

        if ($imagemcapa) {
            // Remove a barra inicial, se existir
            $imagemcapa = ltrim($imagemcapa, '/');

            // Se começa com 'storage/', converte para caminho real
            if (strpos($imagemcapa, 'storage/') === 0) {
                $caminhoFisico = storage_path('app/public/' . substr($imagemcapa, strlen('storage/')));
            } else {
                $caminhoFisico = public_path($imagemcapa);
            }

            // Só anexa se o arquivo existir
            if (file_exists($caminhoFisico)) {
                $this->attach($caminhoFisico, [
                    'as' => 'capa.jpg',
                    'mime' => 'image/jpeg',
                    'display' => 'inline',
                ]);
                $this->imagemPath = 'cid:capa.jpg';
            } else {
                $this->imagemPath = null;
            }
        }

        return $this->subject('📚 Nova Requisição de Livro')
            ->markdown('emails.nova_requisicao')
            ->with([
                'imagemPath' => $this->imagemPath ?? null,
            ]);
    }
}
