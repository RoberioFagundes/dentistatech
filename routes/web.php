<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FilaAgendamentoEsperaController;
// use App\Http\Controllers\FilaEsperaAgendamentoController;
use App\Http\Controllers\RemacacaoController;
use App\Http\Controllers\FilaAtendimentoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Página Inicial
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Centralizado (Com redirecionamento por tipo de usuário)
Route::get('/dashboard', function () {
    // O middleware 'auth' já garante que o usuário está logado, 
    // então podemos acessar o Auth::user() diretamente com segurança.
    return match (Auth::user()->tipo) {
        'recepcionista' => view('recepcionista.dashboardRecepcionista'),
        'dentista'      => view('dentista.dashboardDentista'),
        'administrador' => view('administrador.dashboardAdministrador'),
        'paciente'      => view('paciente.dashboardPaciente'),
        default         => abort(403, 'Tipo de usuário não autorizado.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas Protegidas de Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    // Recursos padrão
    Route::resource('paciente', PacienteController::class);
    Route::resource('dentista', DentistaController::class);
    Route::resource('agendamento', AgendamentoController::class);
    Route::resource('procedimento', ProcedimentoController::class);
    Route::resource('financeiro', FinanceiroController::class);
    Route::resource('atendimento', AtendimentoController::class);
    Route::resource('remarcacao', RemacacaoController::class);
    Route::resource('filaAtendimento', FilaAtendimentoController::class);

    // Rotas específicas da Fila de Espera (Mantendo apenas um grupo limpo)
    Route::prefix('filaAgendamentoEspera')->group(function () {
        Route::get('/', [FilaAgendamentoEsperaController::class, 'index'])->name('filaAgendamentoEspera.index');
        Route::get('create', [FilaAgendamentoEsperaController::class, 'create'])->name('filaAgendamentoEspera.create');
        Route::get('/{id}/editar', [FilaAgendamentoEsperaController::class, 'edit'])->name('filaAgendamentoEspera.edit');
        Route::put('/{id}', [FilaAgendamentoEsperaController::class, 'updateFilaEsperaAgendamento'])->name('filaAgendamentoEspera.update');
        Route::delete('/{id}', [FilaAgendamentoEsperaController::class, 'deleteFilaEsperaAgendamento'])->name('filaAgendamentoEspera.destroy');
        Route::post('/salvar', [FilaAgendamentoEsperaController::class, 'storeFilaEsperaAgendamento'])->name('filaAgendamentoEspera.store');
    });
});





require __DIR__.'/auth.php';