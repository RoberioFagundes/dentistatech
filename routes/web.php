<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FilaAgendamentoEsperaController;
use App\Http\Controllers\MarcacaoController;
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


// Protegendo os recursos para que o Auth::user() nunca venha vazio
Route::resource('paciente', PacienteController::class)->middleware('auth');
Route::resource('dentista', DentistaController::class)->middleware('auth');
Route::resource('agendamento', AgendamentoController::class)->middleware('auth');
Route::resource('procedimento', ProcedimentoController::class)->middleware(['auth']);

Route::resource('financeiro', FinanceiroController::class)->middleware(['auth']);
Route::resource('atendimento', AtendimentoController::class)->middleware(['auth']);
Route::resource('filaAgendamentoEspera', FilaAgendamentoEsperaController::class)->middleware(['auth']);
Route::resource('marcacao', MarcacaoController::class)->middleware(['auth']);
Route::resource('filaAtendimento', FilaAtendimentoController::class)->middleware(['auth']);






require __DIR__.'/auth.php';