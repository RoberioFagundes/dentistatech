<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Agendamento;
use App\Models\FilaAtendimento;
use App\Models\Prontuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtendimentoController extends Controller
{
    /**
     * Exibe o histórico de atendimentos realizados na clínica.
     */
    public function index(Request $request)
    {
        // Permite filtrar por paciente ou por dentista específico
        $query = Atendimento::with(['paciente', 'dentista', 'agendamento']);

        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->has('dentista_id')) {
            $query->where('dentista_id', $request->dentista_id);
        }

        $atendimentos = $query->orderBy('data_atendimento', 'desc')->paginate(15);

        return response()->json($atendimentos);
    }

    /**
     * Não costuma ser muito utilizado em APIs de Clínicas, 
     * pois o atendimento geralmente inicia a partir de um botão "Iniciar Consulta" na fila.
     */
    public function create()
    {
        // Retornaria dados para montar uma tela de abertura manual de atendimento
    }

    /**
     * Inicia oficialmente o atendimento clínico (Abertura da Consulta).
     */
    public function store(Request $request)
    {
        // Valida os dados iniciais do atendimento
        $dadosValidados = $request->validate([
            'paciente_id'    => 'required|exists:pacientes,id',
            'dentista_id'    => 'required|exists:dentistas,id',
            'agendamento_id' => 'nullable|exists:agendamentos,id',
            'observacoes_iniciais' => 'nullable|string'
        ]);

        // Usa uma Transaction para garantir que todas as tabelas sejam atualizadas juntas com segurança
        $atendimento = DB::transaction(function () use ($dadosValidados, $request) {
            
            // 1. Cria o registro do atendimento com status 'Em Andamento'
            $atendimento = Atendimento::create(array_merge($dadosValidados, [
                'data_atendimento' => now(),
                'status_atendimento' => 'Em Andamento'
            ]));

            // 2. Se veio de um agendamento prévio, atualiza o status dele para 'Atendido'
            if (!empty($dadosValidados['agendamento_id'])) {
                Agendamento::where('id', $dadosValidados['agendamento_id'])
                    ->update(['status' => 'Atendido']);
            }

            // 3. Se o paciente estava na fila eletrônica de espera da recepção, atualiza para 'Em Atendimento'
            FilaAtendimento::where('paciente_id', $dadosValidados['paciente_id'])
                ->where('status_fila', 'Chamado')
                ->update(['status_fila' => 'Em Atendimento']);

            return $atendimento;
        });

        return response()->json([
            'message' => 'Atendimento iniciado com sucesso!',
            'data' => $atendimento
        ], 201);
    }

    /**
     * Exibe os detalhes de um atendimento específico (útil para auditoria ou revisão).
     */
    public function show(Atendimento $atendimento)
    {
        $atendimento->load(['paciente', 'dentista', 'prontuario']);
        return response()->json($atendimento);
    }

    /**
     * Carrega a tela ou os dados para o dentista finalizar a consulta.
     */
    public function edit(Atendimento $atendimento)
    {
        $atendimento->load(['paciente', 'dentista']);
        return response()->json($atendimento);
    }

    /**
     * Finaliza o atendimento, salvando a evolução clínica no Prontuário.
     */
    public function update(Request $request, Atendimento $atendimento)
    {
        // Valida o preenchimento obrigatório da evolução clínica (Prontuário) ao encerrar
        $request->validate([
            'status_atendimento'  => 'required|string|in:Finalizado,Suspenso',
            'evolucao_clinica'    => 'required|string|min:10', // Exige um relato mínimo do procedimento
            'prescricao_medica'   => 'nullable|string',
            'observacoes_finais'  => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $atendimento) {
            
            // 1. Atualiza o status do Atendimento
            $atendimento->update([
                'status_atendimento' => $request->status_atendimento,
                'observacoes' => $request->observacoes_finais,
                'hora_fim' => now() // Registra a hora exata do encerramento
            ]);

            // 2. Cria automaticamente o Prontuário de evolução amarrado a este atendimento
            Prontuario::create([
                'paciente_id'    => $atendimento->paciente_id,
                'dentista_id'    => $atendimento->dentista_id,
                'atendimento_id' => $atendimento->id,
                'descricao'      => $request->evolucao_clinica,
                'prescricao'     => $request->prescricao_medica,
                'data_registro'  => now()
            ]);

            // 3. Remove o paciente de vez da Fila de Atendimento do dia, marcando como concluído
            FilaAtendimento::where('paciente_id', $atendimento->paciente_id)
                ->where('status_fila', 'Em Atendimento')
                ->update(['status_fila' => 'Atendido']);
        });

        return response()->json([
            'message' => 'Atendimento finalizado e prontuário registrado com sucesso!'
        ]);
    }

    /**
     * Remove ou cancela um registro de atendimento.
     */
    public function destroy(Atendimento $atendimento)
    {
        // Se o atendimento já foi finalizado e gerou prontuário legal, impede a exclusão física
        if ($atendimento->status_atendimento === 'Finalizado') {
            return response()->json(['error' => 'Não é permitido excluir um atendimento já finalizado por motivos de histórico legal.'], 422);
        }

        // Caso contrário, desfaz os status e remove
        DB::transaction(function () use ($atendimento) {
            if ($atendimento->agendamento_id) {
                // Devolve o agendamento para o estado anterior
                Agendamento::where('id', $atendimento->agendamento_id)->update(['status' => 'Agendado']);
            }
            $atendimento->delete();
        });

        return response()->json(['message' => 'Registro de atendimento removido com sucesso.']);
    }
}