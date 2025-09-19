<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Defina os comandos Artisan da aplicação.
     */
    protected $commands = [
        // Se precisar registrar manualmente:
        // \App\Console\Commands\EnviarReminders::class,
    ];

    /**
     * Defina a programação dos comandos.
     */


    protected function schedule(Schedule $schedule): void
    {
    $schedule->command('requisicoes:enviar-reminders')->dailyAt('08:00');
    $schedule->command('app:verificar-carrinhos-abandonados')->hourly();

        $schedule->call(function () {
            info('Tarefa agendada executada com sucesso!');
        })->everyMinute();
    }

    /**
     * Registre os bindings para o Artisan.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
