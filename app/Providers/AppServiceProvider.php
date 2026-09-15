<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Agendamento;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartilha agendamentos apenas nas views relevantes (ajuste os nomes conforme seu projeto)
        View::composer(['agendamentos.index', 'dashboard'], function ($view) {
            if (Auth::check() && Auth::user()->tipo === 'dentista') {
                $agendamentos = Agendamento::with(['paciente', 'dentista'])
                    ->where('dentista_id', Auth::id())
                    ->paginate(10);

                $view->with('agendamentos', $agendamentos);
            }
        });

        // 🔒 BLOQUEIA INATIVOS — REGRA GLOBAL
        Gate::before(function ($user, $ability) {
            if (!$user->ativo) {
                abort(403, 'Cadastro desativado. Acesso negado.');
            }
        });

        // 🟢 Administrador / Gestor — TUDO da clínica
        Gate::define('admin-only', function ($user) {
            return $user->ativo
                && $user->clinica_id === auth()->user()->clinica_id
                && $user->cargo === 'administrador';
        });

         Gate::define('recepcionista', function ($user) {
            return $user->ativo
                && $user->clinica_id === auth()->user()->clinica_id
                && $user->cargo === 'recepcionista';
        });

        // 🔵 Dentista — SOMENTE seus registros
        Gate::define('dentista-only', function ($user, $registro = null) {
            // Verifica ativo e clínica
            if (!$user->ativo || $user->clinica_id !== auth()->user()->clinica_id) {
                return false;
            }
            // Se não tem registro, só valida cargo
            if (!$registro) {
                return $user->cargo === 'dentista';
            }
            // Se tem registro, verifica se é dele
            return $user->cargo === 'dentista'
                && $registro->user_id === $user->id;
        });

        // 🟡 Recepcionista
        Gate::define('recepcionista', function ($user) {
            return $user->ativo
                && $user->clinica_id === auth()->user()->clinica_id
                && $user->cargo === 'recepcionista';
        });

        // 🟣 Acompanhante / Auxiliar
        Gate::define('acompanhante_dentista', function ($user) {
            return $user->ativo
                && $user->clinica_id === auth()->user()->clinica_id
                && $user->cargo === 'acompanhante_dentista';
        });

        // ✅ Gestor (seu campo cargo)
        Gate::define('gestor', function ($user) {
            return $user->ativo
                && $user->clinica_id === auth()->user()->clinica_id
                && $user->cargo === 'gestor';
        });

        // Proteção de comandos em produção
        Event::listen(function (CommandStarting $event) {
            $comandosProibidos = ['migrate:rollback', 'migrate:reset', 'migrate:fresh', 'db:wipe'];

            if (app()->isProduction() && in_array($event->command, $comandosProibidos)) {
                throw new \RuntimeException("🚫 COMANDO BLOQUEADO: '{$event->command}' não pode ser executado em produção. Dados protegidos.");
            }
        });

        // Paginação com Bootstrap 5
        Paginator::useBootstrapFive();
    }
}
