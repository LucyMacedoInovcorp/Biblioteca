<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carrinho;
use App\Models\Encomenda;
use Carbon\Carbon;

class VerificarCarrinhosAbandonados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verificar-carrinhos-abandonados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Comando verificar-carrinhos-abandonados foi chamado pelo scheduler.');
        $limite = Carbon::now()->subMinute();
        // Buscar carrinhos com itens, cujo último item foi adicionado há mais de 1 minuto, sem encomenda associada
        $carrinhos = Carrinho::whereHas('itens', function($q) use ($limite) {
                $q->where('created_at', '<', $limite);
            })
            ->whereDoesntHave('encomenda')
            ->get();

        if ($carrinhos->isEmpty()) {
            $this->info('Nenhum carrinho abandonado encontrado para notificar.');
            return;
        }

        foreach ($carrinhos as $carrinho) {
            // Aqui será enviado o e-mail posteriormente
            $this->info('Carrinho abandonado: ' . $carrinho->id . ' do usuário ' . $carrinho->user_id);
            $user = $carrinho->user;
            if ($user && $user->email) {
                \Mail::to($user->email)->send(new \App\Mail\CarrinhoAbandonadoMail($user));
                $this->info('E-mail enviado para: ' . $user->email);
            }
        }
    }
}
