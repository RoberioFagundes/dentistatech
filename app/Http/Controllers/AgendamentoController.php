<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
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
        // Permite filtrar por data específica ou pega a data de hoje por padrão
        $dataFiltro = $request->get('data', today()->toDateString());

        // Carrega os agendamentos com os dados de Paciente e Dentista acoplados (evita gargalo N+1)
        $agendamentos = Agendamento::with(['paciente', 'dentista'])
            ->whereDate('data_hora_inicio', $dataFiltro)
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        return response()->json($agendamentos);
        // Caso use Blade: return view('agendamentos.index', compact('agendamentos', 'dataFiltro'));
    }

    /**
     * Exibe o formulário de criação (Auxilia trazendo os seletores necessários).
     */
    public function create()
    {
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();

        return response()->json(compact('pacientes', 'dentistas'));
        // Caso use Blade: return view('agendamentos.create', compact('pacientes', 'dentistas'));
    }

    /**
     * Salva um novo agendamento validando regras clínicas básicas.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados de entrada
        $dadosValidados = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'dentista_id'      => 'required|exists:dentistas,id',
            'data_hora_inicio' => 'required|date|after:now',
            'data_hora_fim'    => 'required|date|after:data_hora_inicio',
            'status'           => 'required|string|in:Agendado,Confirmado,Cancelado,Faltou,Atendido',
            'observacoes'      => 'nullable|string'
        ]);

        $dataApenas = date('Y-m-d', strtotime($request->data_hora_inicio));

        // 2. Regra de Negócio: Impede marcação em feriados que bloqueiam a agenda
        $eFeriado = Feriado::where('data', $dataApenas)->where('bloqueia_agenda', true)->exists();
        if ($eFeriado) {
            return response()->json(['error' => 'Não é possível agendar nesta data. A clínica estará fechada devido a um feriado/recesso.'], 422);
        }

        // 3. Regra de Negócio: Verifica conflito de horário na agenda do mesmo dentista
        $conflitoDentista = Agendamento::where('dentista_id', $request->dentista_id)
            ->where('status', '!=', 'Cancelado')
            ->where(function ($query) use ($request) {
                $query->whereBetween('data_hora_inicio', [$request->data_hora_inicio, $request->data_hora_fim])
                      ->orWhereBetween('data_hora_fim', [$request->data_hora_inicio, $request->data_hora_fim]);
            })->exists();

        if ($conflitoDentista) {
            return response()->json(['error' => 'O dentista selecionado já possui um compromisso neste mesmo horário.'], 422);
        }

        // 4. Criação do registro
        $agendamento = Agendamento::create($dadosValidados);

        return response()->json([
            'message' => 'Agendamento realizado com sucesso!',
            'data' => $agendamento
        ], 21);
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
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();

        return response()->json(compact('agendamento', 'pacientes', 'dentistas'));
    }

    /**
     * Atualiza os dados ou o Status do agendamento (Ex: Mudar de 'Agendado' para 'Confirmado').
     */
    public function update(Request $request, Agendamento $agendamento)
    {
        $dadosValidados = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'dentista_id'      => 'required|exists:dentistas,id',
            'data_hora_inicio' => 'required|date',
            'data_hora_fim'    => 'required|date|after:data_hora_inicio',
            'status'           => 'required|string|in:Agendado,Confirmado,Cancelado,Faltou,Atendido',
            'observacoes'      => 'nullable|string'
        ]);

        // Validação de conflito de horário ignorando o próprio ID do agendamento atual
        $conflitoDentista = Agendamento::where('dentista_id', $request->dentista_id)
            ->where('id', '!=', $agendamento->id)
            ->where('status', '!=', 'Cancelado')
            ->where(function ($query) use ($request) {
                $query->whereBetween('data_hora_inicio', [$request->data_hora_inicio, $request->data_hora_fim])
                      ->orWhereBetween('data_hora_fim', [$request->data_hora_inicio, $request->data_hora_fim]);
            })->exists();

        if ($conflitoDentista) {
            return response()->json(['error' => 'Não foi possível reagendar. O dentista possui outro compromisso neste horário.'], 422);
        }

        $agendamento->update($dadosValidados);

        return response()->json([
            'message' => 'Agendamento atualizado com sucesso!',
            'data' => $agendamento
        ]);
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

        return response()->json(['message' => 'Agendamento cancelado com sucesso no sistema.']);
    }
}