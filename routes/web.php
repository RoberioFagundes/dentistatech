<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FilaAgendamentoEsperaController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\RemarcacaoController; // ✅ NOME CORRETO COM R
use App\Http\Controllers\FilaAtendimentoController;
use App\Http\Controllers\PagamentoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// Página Inicial
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard Centralizado
Route::get('/dashboard', function () {
    return match (Auth::user()->tipo) {
        'recepcionista' => view('recepcionista.dashboardRecepcionista'),
        'dentista'      => view('dentista.dashboardDentista'),
        'administrador' => view('administrador.dashboardAdministrador'),
        'paciente'      => view('paciente.dashboardPaciente'),
        default         => abort(403, 'Tipo de usuário não autorizado.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('paciente', PacienteController::class);
    Route::resource('dentista', DentistaController::class);
    Route::resource('agendamento', AgendamentoController::class);
    Route::resource('procedimento', ProcedimentoController::class);

    // Rotas personalizadas de Pagamento (ANTES do resource)
    Route::get('/pagamento/confirmacao/{id}', [PagamentoController::class, 'pagamento_confirmacao'])->name('pagamento.confirmacao');
    Route::post('/pagamento/confirmar/{id}', [PagamentoController::class, 'confirmar'])->name('pagamento.confirmar');
    Route::resource('pagamento', PagamentoController::class)->except(['show']);

    Route::resource('financeiro', FinanceiroController::class);
    Route::resource('atendimento', AtendimentoController::class);

    // ✅ Rota de Remarcação com nome correto
    Route::resource('remarcacao', RemarcacaoController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::resource('filaAtendimento', FilaAtendimentoController::class);
    Route::resource('orcamento', OrcamentoController::class);
    Route::get('/atendimento/{atendimento}/pdf', [AtendimentoController::class, 'gerarPdf'])->name('atendimento.pdf');

    // Rotas Fila de Espera
    Route::prefix('filaAgendamentoEspera')->group(function () {
        Route::get('/', [FilaAgendamentoEsperaController::class, 'index'])->name('filaAgendamentoEspera.index');
        Route::get('create', [FilaAgendamentoEsperaController::class, 'create'])->name('filaAgendamentoEspera.create');
        Route::get('/{id}/editar', [FilaAgendamentoEsperaController::class, 'edit'])->name('filaAgendamentoEspera.edit');
        Route::put('/{id}', [FilaAgendamentoEsperaController::class, 'updateFilaEsperaAgendamento'])->name('filaAgendamentoEspera.update');
        Route::delete('/{id}', [FilaAgendamentoEsperaController::class, 'deleteFilaEsperaAgendamento'])->name('filaAgendamentoEspera.destroy');
        Route::post('/salvar', [FilaAgendamentoEsperaController::class, 'storeFilaEsperaAgendamento'])->name('filaAgendamentoEspera.store');
    });

    Route::delete('/fila/agendar-selecionados', [FilaAgendamentoEsperaController::class, 'agendarSelecionados'])->name('fila.agendarSelecionados');
    Route::delete('/filaAgendamentoEspera/{id}', [FilaAgendamentoEsperaController::class, 'deleteFilaEsperaAgendamento'])->name('fila-delete-espera');
    Route::post('/fila-atendimento/adicionar', [FilaAtendimentoController::class, 'store'])->name('fila.adicionar');
});

require __DIR__ . '/auth.php';