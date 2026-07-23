<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FilaAgendamentoEsperaController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\RemarcacaoController;
use App\Http\Controllers\FilaAtendimentoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\WebhookAsaasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// Página Inicial
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas públicas de Planos e Assinatura (sempre liberadas)
Route::get('/planos', [PlanoController::class, 'index'])->name('planos');
Route::post('/assinatura/criar', [PlanoController::class, 'criarAssinatura'])->name('assinatura.criar');

// Webhook do Asaas (NÃO pode ter middleware de auth!)
Route::post('webhook/asaas', [WebhookAsaasController::class, 'receber'])->name('webhook.asaas');

// Dashboard Centralizado
Route::get('/dashboard', function () {
    return match (Auth::user()->tipo) {
        'recepcionista' => view('recepcionista.dashboardRecepcionista'),
        'dentista'      => view('dentista.dashboardDentista'),
        'administrador' => view('dentista.dashboardDentista'),
        'paciente'      => view('paciente.dashboardPaciente'),
        default         => abort(403, 'Tipo de usuário não autorizado.'),
    };
})->middleware(['auth', 'verified', \App\Http\Middleware\VerificarAssinatura::class])->name('dashboard');

// Rotas de Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔐 Todas as rotas protegidas por Assinatura/Teste
Route::middleware(['auth', 'verificar.assinatura'])->group(function () {
    Route::resource('paciente', PacienteController::class);
    Route::resource('dentista', DentistaController::class);
    Route::resource('agendamento', AgendamentoController::class);
    Route::resource('procedimento', ProcedimentoController::class);

    // Rotas de Pagamento
    Route::get('/pagamento/confirmacao/{id}', [PagamentoController::class, 'pagamento_confirmacao'])->name('pagamento.confirmacao');
    Route::post('/pagamento/confirmar/{id}', [PagamentoController::class, 'confirmar'])->name('pagamento.confirmar');
    Route::resource('pagamento', PagamentoController::class)->except(['show']);

    Route::resource('financeiro', FinanceiroController::class);
    Route::resource('atendimento', AtendimentoController::class);
    Route::get('/atendimento/{atendimento}/pdf', [AtendimentoController::class, 'gerarPdf'])->name('atendimento.pdf');

    // Remarcação
    Route::resource('remarcacao', RemarcacaoController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::resource('filaAtendimento', FilaAtendimentoController::class);
    Route::resource('orcamento', OrcamentoController::class);

    // Fila de Espera
    Route::prefix('filaAgendamentoEspera')->name('filaAgendamentoEspera.')->group(function () {
        Route::get('/', [FilaAgendamentoEsperaController::class, 'index'])->name('index');
        Route::get('create', [FilaAgendamentoEsperaController::class, 'create'])->name('create');
        Route::get('/{id}/editar', [FilaAgendamentoEsperaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FilaAgendamentoEsperaController::class, 'updateFilaEsperaAgendamento'])->name('update');
        Route::delete('/{id}', [FilaAgendamentoEsperaController::class, 'deleteFilaEsperaAgendamento'])->name('destroy');
        Route::post('/salvar', [FilaAgendamentoEsperaController::class, 'storeFilaEsperaAgendamento'])->name('store');
    });

    Route::delete('/fila/agendar-selecionados', [FilaAgendamentoEsperaController::class, 'agendarSelecionados'])->name('fila.agendarSelecionados');
    Route::delete('/filaAgendamentoEspera/{id}', [FilaAgendamentoEsperaController::class, 'deleteFilaEsperaAgendamento'])->name('fila-delete-espera');
    Route::post('/fila-atendimento/adicionar', [FilaAtendimentoController::class, 'store'])->name('fila.adicionar');
});

// Rotas só para Dentistas/Administradores
Route::middleware(['auth', 'dentista', 'verificar.assinatura'])->group(function () {
    Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
    Route::get('/funcionarios/novo', [FuncionarioController::class, 'create'])->name('funcionarios.create');
    Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
});

require __DIR__ . '/auth.php';