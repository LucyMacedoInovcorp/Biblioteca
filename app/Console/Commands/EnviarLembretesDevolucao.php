<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Requisicao;
use App\Notifications\PrazoDevolucaoReminder;

class EnviarLembretesDevolucao extends Command
{
    protected $signature = 'requisicoes:enviar-reminders';
    protected $description = 'Envia email 1 dia antes do prazo de devolução';

    public function handle()
    {
        $amanha = Carbon::tomorrow();        
        $amanha = Carbon::create(2025, 9, 8);

        // Busca requisições ativas e verifica se o prazo_devolucao é amanhã
        $requisicoes = Requisicao::where('ativo', true)
            ->with(['user','livro'])
            ->get()
            ->filter(fn($req) => $req->prazo_devolucao?->isSameDay($amanha));

        foreach ($requisicoes as $req) {
            if ($req->user) {
                $req->user->notify(new PrazoDevolucaoReminder($req));
                $this->info("Reminder enviado para {$req->user->email} (Req #{$req->id})");
            }
        }

        if ($requisicoes->isEmpty()) {
            $this->info("Nenhum reminder para enviar hoje.");
        }

        return self::SUCCESS;
    }


}
