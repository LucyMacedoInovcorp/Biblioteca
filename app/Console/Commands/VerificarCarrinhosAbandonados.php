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
        $limite = Carbon::now()->subMinute();
        // Buscar carrinhos com itens, criados há mais de 1 minuto, sem encomenda associada
        $carrinhos = Carrinho::whereHas('itens')
            ->whereDoesntHave('encomenda')
            ->where('created_at', '<', $limite)
            ->get();

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
