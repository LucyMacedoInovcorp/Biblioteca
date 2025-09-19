<?php

namespace App\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Carrinho;
use App\Mail\CarrinhoAbandonadoMail;
use Illuminate\Support\Facades\Mail;


class EnviarCarrinhoAbandonadoJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $carrinho;

    /**
     * Create a new job instance.
     */
    public function __construct(Carrinho $carrinho)
    {
        $this->carrinho = $carrinho;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verifica se o carrinho ainda está abandonado (exemplo: não finalizado)
        if ($this->carrinho->isAbandonado()) {
            $user = $this->carrinho->user;
            Mail::to($user->email)->send(new CarrinhoAbandonadoMail($user, $this->carrinho));
        }

            try {
        if ($this->carrinho->isAbandonado()) {
            $user = $this->carrinho->user;
            \Mail::to($user->email)->send(new \App\Mail\CarrinhoAbandonadoMail($user, $this->carrinho));
        }
    } catch (\Throwable $e) {
        \Log::error('Erro ao enviar carrinho abandonado: ' . $e->getMessage(), ['exception' => $e]);
    }
    }
}
