<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Avaliacao;

class SendReviewMail extends Mailable
{
    use Queueable, SerializesModels;
    public $avaliacao;
    public function __construct(Avaliacao $avaliacao)
    {
        $this->avaliacao = $avaliacao;
    }

    public function build()
    {
        $livro = \App\Models\Livro::find($this->avaliacao->livro_id);
        return $this->subject('📚 Nova Avalização')
            ->markdown('emails.reviews.sendreview', [
                'avaliacao' => $this->avaliacao,
                'livro' => $livro
            ]);
    }
}
