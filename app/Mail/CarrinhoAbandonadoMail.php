<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class CarrinhoAbandonadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $livro;
    public $imagemPath;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $carrinho)
    {
        $this->user = $user;
        // Carrega os itens com o relacionamento 'livro'
        $item = $carrinho->itens()->with('livro')->latest()->first();
        $this->livro = $item ? $item->livro : null;
        $this->imagemPath = $this->livro && $this->livro->capa
        ? asset('storage/capas/' . $this->livro->capa)
        : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Precisa de ajuda com seu carrinho?',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.carrinho_abandonado',
            with: [
                'user' => $this->user,
                'livro' => $this->livro,
                'imagemPath' => $this->imagemPath,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
