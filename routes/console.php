<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Roda o backup completo todos os dias à 1h da manhã
Schedule::command('backup:run')->dailyAt('01:00');

// Limpa backups muito antigos todos os dias à 2h da manhã
Schedule::command('backup:clean')->dailyAt('02:00');

// Seu comando para verificar testes vencidos
Schedule::command('verificar:testes-expirados')->dailyAt('00:05');


