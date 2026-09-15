<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ProcedimentoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FilaAgendamentoEsperaController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\RemarcacaoController;
use App\Http\Controllers\FilaAtendimentoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\WebhookAsaasController;
use App\Http\Controllers\AssinaturaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// Página Inicial
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Webhook do Asaas (Pública)
Route::post('webhook/asaas', [WebhookAsaasController::class, 'receber'])->name('webhook.asaas');

// Rotas de Assinatura e Planos (Exigem apenas Auth, NUNCA use o VerificarAssinatura aqui para evitar loop)
Route::middleware('auth')->group(function () {
    Route::get('/planos', [PlanoController::class, 'index'])->name('planos');
    Route::post('/assinatura/criar', [AssinaturaController::class, 'criar'])->name('assinatura.criar');
    Route::get('/assinatura/confirmar', [AssinaturaController::class, 'confirmar'])->name('assinatura.confirmar');
});




// Grupo principal protegido por Assinatura (Sistema Administrativo/Clínica)
Route::middleware(['auth', \App\Http\Middleware\VerificarAssinatura::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/fila-espera/create', [FilaAgendamentoEsperaController::class, 'create'])->name('create-fila-agendamento');
    Route::post('/fila-espera/agendar-selecionados', [FilaAgendamentoEsperaController::class, 'agendarSelecionados'])->name('fila.agendarSelecionados');

    Route::resource('paciente', PacienteController::class);
    Route::resource('dentista', DentistaController::class);
    Route::resource('agendamento', AgendamentoController::class);
    Route::resource('procedimento', ProcedimentoController::class);

    // ✅ Corrigido: Removido o middleware duplicado e garantido que o dashboard exige a assinatura
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pagamento/confirmacao/{id}', [PagamentoController::class, 'pagamento_confirmacao'])->name('pagamento.confirmacao');
    Route::post('/pagamento/confirmar/{id}', [PagamentoController::class, 'confirmar'])->name('pagamento.confirmar');
    Route::get('/pagamento/create/{orcamento}', [PagamentoController::class, 'create'])->name('pagamento.create');

    // ✅ Se já tiver o resource, exclua a duplicata ou use except:
    Route::resource('pagamento', PagamentoController::class)->except(['create']);

    // ✅ Rotas de Orçamento organizadas e sem conflitos
    Route::get('/orcamento/create/{atendimento}', [OrcamentoController::class, 'create'])->name('orcamento.create');
    Route::resource('orcamento', OrcamentoController::class)->except(['create']);

    Route::resource('financeiro', FinanceiroController::class);
    Route::resource('atendimento', AtendimentoController::class);
    Route::get('/atendimento/{atendimento}/pdf', [AtendimentoController::class, 'gerarPdf'])->name('atendimento.pdf');

    Route::resource('remarcacao', RemarcacaoController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('filaAtendimento', FilaAtendimentoController::class);


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

    Route::get('/funcionarios', [FuncionarioController::class, 'index'])->name('funcionarios.index');
    Route::get('/funcionarios/novo', [FuncionarioController::class, 'create'])->name('funcionarios.create');
    Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
    Route::get('/funcionarios/{funcionario}/editar', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
    Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
    Route::delete('/funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
    Route::get('/funcionarios/{funcionario}', [FuncionarioController::class, 'show'])->name('funcionarios.show');
});

require __DIR__ . '/auth.php';
