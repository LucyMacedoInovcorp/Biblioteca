<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrazoDevolucaoReminder extends Notification
{
    use Queueable;

    public function __construct(public $requisicao) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        $livro = $this->requisicao->livro->nome ?? 'o livro';
        $prazo = $this->requisicao->prazo_devolucao?->format('d/m/Y') ?? '—';

        return (new MailMessage)
            ->subject('📅 Lembrete: Devolução do Livro Amanhã')
            ->greeting("Olá, {$notifiable->name}")
            ->line("Este é um lembrete de que o prazo de devolução de **{$livro}** é amanhã.")
            ->line("Data limite: **{$prazo}**")
            ->line('Obrigado pela colaboração!')
            ->salutation('BibliON 📚');
    }
}

