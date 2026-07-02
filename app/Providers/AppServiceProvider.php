<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bloqueia comandos destrutivos em produção
        Event::listen(function (CommandStarting $event) {
            $comandosProibidos = [
                'migrate:rollback',
                'migrate:reset',
                'migrate:fresh',
                'db:wipe'
            ];

            if (app()->isProduction() && in_array($event->command, $comandosProibidos)) {
                throw new \Exception("ALERTA: O comando '{$event->command}' está permanentemente desativado no ambiente de produção para proteger os dados.");
            }
        });
    }
}
