<?php

namespace App\Http\Controllers; // Verifique se este namespace está correto

use App\Http\Controllers\Controller; // IMPORTANTE: Importe a classe base
use Illuminate\Http\Request;
use App\Models\FilaAtendimento;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Auth;

class FilaAtendimentoController extends Controller
{
    /**
     * Exibe a listagem da fila de espera do dia atual.
     */

    public function index(Request $request)
    {
        // Verifica se o usuário está logado
        $user = auth()->user();

        // Consulta filtrando pela data, status e opcionalmente pelo ID do dentista
        $fila = FilaAtendimento::with(['paciente', 'dentista'])
            ->whereDate('chegada_em', today())
            ->whereIn('status_fila', ['AGUARDANDO', 'EM_ATENDIMENTO'])
            // Se você precisar que o dentista veja APENAS os pacientes dele:
            // ->where('dentista_id', $user->dentista_id) 
            ->orderBy('prioridade', 'desc')
            ->orderBy('chegada_em', 'asc')
            ->paginate(15);

        return view('dentista.filaAtendimento.index', compact('fila'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        // Retornaria dados de apoio para a recepção colocar alguém manualmente na fila
    }

    /**
     * Registra a chegada do paciente na recepção (Entrada na Fila).
     */
    public function store(Request $request)
    {
        $request->validate([
            'agendamento_id' => 'required|exists:agendamentos,id',
        ]);

        // 1. Busca o agendamento para garantir que ele existe e obter o ID do paciente vinculado
        $agendamento = Agendamento::findOrFail($request->agendamento_id);

        // 2. Verifica se o agendamento já está na fila
        $filaExistente = FilaAtendimento::where('agendamento_id', $request->agendamento_id)
            ->whereIn('status_fila', ['AGUARDANDO', 'EM_ATENDIMENTO'])
            ->first();

        if ($filaExistente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Este paciente já está na fila.');
        }

        // 3. Cria o registro na fila salvando tanto o agendamento_id quanto o paciente_id
        $fila = FilaAtendimento::create([
            'agendamento_id' => $request->agendamento_id,
            'paciente_id' => $agendamento->paciente_id, // <--- ADICIONADO AQUI
            'status_fila' => 'AGUARDANDO',
            'prioridade' => 0,
            'chegada_em' => now(),
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('filaAtendimento.index')
            ->with('success', 'Paciente adicionado à fila com sucesso!');
    }

    /**
     * Exibe os detalhes de um registro específico da fila.
     */
    public function show(FilaAtendimento $filaAtendimento)
    {
        $filaAtendimento->load(['paciente', 'dentista']);
        return response()->json($filaAtendimento);
    }

    /**
     * Exibe os dados do registro para edição.
     */
    public function edit(FilaAtendimento $filaAtendimento)
    {
        return response()->json($filaAtendimento);
    }

    /**
     * Atualiza as informações do fluxo ou realiza a transição de status (Chamar Painel / Iniciar Consulta).
     */
    public function update(Request $request, FilaAtendimento $filaAtendimento)
    {
        // Geralmente usado para atualizar o status do fluxo da recepção/consultório
        // Ex: De 'Aguardando' para 'Chamado' (Piscar no painel da TV da recepção)
        $dadosValidados = $request->validate([
            'status_fila' => 'required|string|in:Aguardando,Chamado,Em Atendimento,Atendido,Desistiu',
            'prioridade' => 'nullable|integer|in:0,1',
            'observacoes' => 'nullable|string|max:255'
        ]);

        // Atualiza os dados dinamicamente
        $filaAtendimento->update($dadosValidados);

        return response()->json([
            'message' => 'Fluxo da fila atualizado com sucesso!',
            'data' => $filaAtendimento
        ]);
    }

    /**
     * Remove o paciente da fila de espera (Casos de desistência ou erro de digitação).
     */
    public function destroy(FilaAtendimento $filaAtendimento)
    {
        // Se o paciente já estiver em atendimento dentro do consultório, bloqueia a remoção seca
        if ($filaAtendimento->status_fila === 'Em Atendimento') {
            return response()->json([
                'error' => 'O paciente já está em consulta com o dentista e não pode ser removido da fila desta forma.'
            ], 422);
        }

        // Se veio de um agendamento, desfaz a confirmação de presença se necessário
        if ($filaAtendimento->agendamento_id) {
            Agendamento::where('id', $filaAtendimento->agendamento_id)->update(['status' => 'Agendado']);
        }

        // Se preferir manter o histórico para relatórios de desistência, mude para:
        // $filaAtendimento->update(['status_fila' => 'Desistiu']);
        $filaAtendimento->delete();

        return response()->json(['message' => 'Paciente removido da fila de atendimento com sucesso.']);
    }
}
