<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\Procedimento;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Feriado;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Exibe a listagem de agendamentos (Agenda do Dia/Mês).
     */
 public function index(Request $request)
{
    // Captura a data se o usuário quiser filtrar, mas não a torna obrigatória por padrão
    $dataFiltro = $request->get('data');

    // Inicia a query trazendo as relações corretas e filtrando pelo usuário logado
    $query = Agendamento::with([
        'paciente',
        'dentista',
        'atendimento' // <-- Corrigido para 'atendimento', que é o nome real do método na sua Model Agendamento
    ])
    ->where('user_id', auth()->id()); // Filtra apenas os agendamentos do usuário autenticado

    // Se o usuário passar uma data no filtro da tela, aplica o filtro. 
    // Se não passar nada, traz TODOS os agendamentos de todas as datas.
    if ($dataFiltro) {
        $query->whereDate('data', $dataFiltro);
    }

    // Ordena primeiro pela data mais recente e depois pelo horário de início
    $agendamentos = $query->orderBy('data', 'desc')
                          ->orderBy('hora_inicio', 'asc')
                          ->get();

    return view('dentista.agendamento.index', compact('agendamentos', 'dataFiltro'));
}

    /**
     * Exibe o formulário de criação (Auxilia trazendo os seletores necessários).
     */
    public function create()
    {
        // 1. Busca os dados necessários para preencher os <select> do formulário
        $pacientes = Paciente::all();
        $dentistas = Dentista::all();
        $procedimentos = Procedimento::all(); // <--- Faltava essa linha!

        // 2. Envia TODAS as variáveis para a view dentro do compact
        return view('dentista.agendamento.formulario', compact('pacientes', 'dentistas', 'procedimentos'));

        // return response()->json(compact('pacientes', 'dentistas'));
        // Caso use Blade: return view('agendamentos.create', compact('pacientes', 'dentistas'));
    }

    /**
     * Salva um novo agendamento validando regras clínicas básicas.
     */
    public function store(Request $request)
    {
        // 1. Validação estruturada com regras seguras (dentista_id removido daqui)
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'procedimento_id' => 'nullable|exists:procedimentos,id',
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'status' => 'required|in:AGENDADO,CONFIRMADO,EM_ATENDIMENTO',
            'observacao' => 'nullable|string|max:45',
        ]);

        // Injeta o ID do usuário autenticado como o dentista_id
        $dadosValidados['dentista_id'] = auth()->id();
        $dadosValidados['user_id']     = auth()->id();

        // 2. Regra de Negócio: Impede marcação em feriados/recessos que bloqueiam a agenda
        $eFeriado = Feriado::where('data', $dadosValidados['data'])
            ->where('bloqueia_agenda', true)
            ->exists();

        if ($eFeriado) {
            return back()
                ->withErrors(['data' => 'Não é possível agendar nesta data. A clínica estará fechada devido a um feriado.'])
                ->withInput();
        }

        // 3. Regra de Negócio: Verifica conflitos de horários (Intersecção de intervalos)
        $conflitoDentista = Agendamento::where('dentista_id', $dadosValidados['dentista_id'])
            ->where('data', $dadosValidados['data'])
            ->where('status', '!=', 'CANCELADO')
            ->where(function ($query) use ($dadosValidados) {
                $query->where('hora_inicio', '<', $dadosValidados['hora_fim'])
                    ->where('hora_fim', '>', $dadosValidados['hora_inicio']);
            })
            ->exists();

        if ($conflitoDentista) {
            return back()
                ->withErrors(['hora_inicio' => 'Você já possui um agendamento neste horário ou há sobreposição de minutos.'])
                ->withInput();
        }

        // 4. Salva o registro usando a model tratada com o dentista_id injetado
        Agendamento::create($dadosValidados);

        return redirect()
            ->route('agendamento.index') // Ajustado o nome da rota conforme o método index anterior
            ->with('success', 'Agendamento realizado com sucesso!');
    }

    /**
     * Exibe um agendamento específico com todos os detalhes do paciente e profissional.
     */
    public function show(Agendamento $agendamento)
    {
        // Carrega os relacionamentos do modelo recebido por Route Model Binding
        $agendamento->load(['paciente', 'dentista']);

        return response()->json($agendamento);
    }

    /**
     * Exibe o formulário de edição pré-carregando os dados.
     */
  public function edit(Agendamento $agendamento)
    {
        
        if ($agendamento->user_id != auth()->id()) {
            abort(404);
        }

        $pacientes = Paciente::where('user_id', auth()->id())
            ->orderBy('nome')
            ->get();

        $dentistas = Dentista::where('user_id', auth()->id())
            ->orderBy('nome')
            ->get();

        $procedimentos = Procedimento::where('user_id', auth()->id())
            ->orderBy('nome')
            ->get();

        return view('dentista.agendamento.formulario', compact(
            'agendamento',
            'pacientes',
            'dentistas',
            'procedimentos'
        ));
    }
    /**
     * Atualiza os dados ou o Status do agendamento (Ex: Mudar de 'Agendado' para 'Confirmado').
     */
   public function update(Request $request, Agendamento $agendamento)
    {
        $dados = $request->validate([
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required|after:hora_inicio',
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'procedimento_id' => 'nullable|exists:procedimentos,id',
            'status' => 'required|in:AGENDADO,CONFIRMADO,EM_ATENDIMENTO,CANCELADO',
            'observacao' => 'nullable|string|max:45',
        ]);

        // Verifica conflito de horário
        $conflito = Agendamento::where('dentista_id', $dados['dentista_id'])
            ->where('id', '!=', $agendamento->id)
            ->where('status', '!=', 'CANCELADO')
            ->where('data', $dados['data'])
            ->where(function ($query) use ($dados) {
                $query->whereBetween('hora_inicio', [$dados['hora_inicio'], $dados['hora_fim']])
                    ->orWhereBetween('hora_fim', [$dados['hora_inicio'], $dados['hora_fim']]);
            })
            ->exists();

        if ($conflito) {
            return back()
                ->withErrors([
                    'hora_inicio' => 'Já existe outro agendamento para este dentista neste horário.'
                ])
                ->withInput();
        }

        $agendamento->update($dados);

        return redirect()
            ->route('agendamento.index')
            ->with('success', 'Agendamento atualizado com sucesso!');
    }
    /**
     * Remove ou cancela o agendamento do banco de dados.
     */
    public function destroy(Agendamento $agendamento)
    {
        // Em sistemas de saúde, muitas vezes prefere-se alterar o status para 'Cancelado'
        // em vez de deletar fisicamente o registro para manter o histórico de auditoria.
        // Se desejar exclusão física, use: $agendamento->delete();

        $agendamento->update(['status' => 'Cancelado']);

        return redirect()
            ->route('agendamento.index') // Ajustado o nome da rota conforme o método index anterior
            ->with('success', 'Agendamento cancelado com sucesso!');
    }
}