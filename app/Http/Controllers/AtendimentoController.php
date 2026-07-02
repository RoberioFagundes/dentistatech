<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Agendamento;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\FilaAtendimento;
use App\Models\Prontuario;
use App\Models\ProcedimentoDente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtendimentoController extends Controller
{
    /**
     * Exibe o histórico de atendimentos realizados na clínica.
     */
    public function index(Request $request)
    {
        $query = Atendimento::with([
            'paciente',
            'dentista',
            'agendamento'
        ])
        ->where('user_id', auth()->id());

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->filled('dentista_id')) {
            $query->where('dentista_id', $request->dentista_id);
        }

        $atendimentos = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pacientes = Paciente::orderBy('nome')->get();

        $dentistas = Dentista::orderBy('nome')->get();

        return view(
            'dentista.atendimento.index',
            compact(
                'atendimentos',
                'pacientes',
                'dentistas'
            )
        );
    }

    /**
     * Tela para abertura manual de atendimento.
     */
    public function create()
    {
        $pacientes = Paciente::orderBy('nome')->get();
        $dentistas = Dentista::orderBy('nome')->get();
        
        // Busca agendamentos do dia que ainda não viraram atendimento
        $agendamentos = Agendamento::whereDate('data', now())
            ->where('status', 'Agendado')
            ->get();

        return view('dentista.atendimento.formulario', compact('pacientes', 'dentistas', 'agendamentos'));
    }

    /**
     * Inicia oficialmente o atendimento clínico (Salva o rascunho/abertura).
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'dentista_id'            => 'required|exists:dentistas,id',
            'agendamento_id'         => 'nullable|exists:agendamentos,id',
            'procedimento_dente_id'  => 'nullable|exists:procedimento_dentes,id',
            'queixa_principal'       => 'required|string|max:255',
            'diagnostico'            => 'nullable|string',
            'tratamento_executado'   => 'nullable|string',
            'observacoes_clinicas'   => 'nullable|string',
        ]);

        $dadosValidados['user_id'] = auth()->id();
        $dadosValidados['created_at'] = now();
        $dadosValidados['status_atendimento'] = 'Em Andamento';

        $atendimento = Atendimento::create($dadosValidados);

        return redirect()->route('atendimento.edit', $atendimento->id)
            ->with('success', 'Atendimento iniciado! Agora você pode mapear os dentes ou finalizar o procedimento.');
    }

    /**
     * Carrega a tela com os dados para o dentista editar ou finalizar a consulta.
     */
    public function edit(Atendimento $atendimento)
    {
        $atendimento->load(['paciente', 'dentista']);
        
        $pacientes = Paciente::orderBy('nome')->get();
        $dentistas = Dentista::orderBy('nome')->get();
        $agendamentos = Agendamento::where('id', $atendimento->agendamento_id)->get();

        return view('dentista.atendimento.formulario', compact('atendimento', 'pacientes', 'dentistas', 'agendamentos'));
    }

    /**
     * Salva as alterações intermediárias do atendimento (Salvar Rascunho).
     */
    public function update(Request $request, Atendimento $atendimento)
    {
        $dadosValidados = $request->validate([
            'paciente_id'            => 'required|exists:pacientes,id',
            'dentista_id'            => 'required|exists:dentistas,id',
            'agendamento_id'         => 'nullable|exists:agendamentos,id',
            'procedimento_dente_id'  => 'nullable|exists:procedimento_dentes,id',
            'queixa_principal'       => 'required|string|max:255',
            'diagnostico'            => 'nullable|string',
            'tratamento_executado'   => 'nullable|string',
            'observacoes_clinicas'   => 'nullable|string',
        ]);

        $dadosValidados['user_id'] = auth()->id();

        $atendimento->update($dadosValidados);

        return redirect()->route('atendimento.index')
            ->with('success', 'Alterações do atendimento salvas com sucesso!');
    }

    /**
     * REGRA DE NEGÓCIO SEPARADA: Finaliza o atendimento de vez e gera o prontuário.
     * Mapear esta função para uma rota específica, ex: Route::post('/atendimento/{atendimento}/finalizar', ...)
     */
    public function finalizar(Request $request, Atendimento $atendimento)
    {
        $request->validate([
            'status_atendimento'  => 'required|string|in:Finalizado,Suspenso',
            'evolucao_clinica'    => 'required|string|min:10', 
            'prescricao_medica'   => 'nullable|string',
            'observacoes_finais'  => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $atendimento) {
            
            // 1. Atualiza o status do Atendimento definitivo
            $atendimento->update([
                'status_atendimento' => $request->status_atendimento,
                'observacoes_clinicas' => $request->observacoes_finais, // Guardado no campo correto da tabela
                'hora_fim' => now() 
            ]);

            // 2. Cria o Prontuário clínico permanente
            Prontuario::create([
                'paciente_id'    => $atendimento->paciente_id,
                'dentista_id'    => $atendimento->dentista_id,
                'atendimento_id' => $atendimento->id,
                'descricao'      => $request->evolucao_clinica,
                'prescricao'     => $request->prescricao_medica,
                'data_registro'  => now()
            ]);

            // 3. Atualiza a fila de atendimento
            FilaAtendimento::where('paciente_id', $atendimento->paciente_id)
                ->where('status_fila', 'Em Atendimento')
                ->update(['status_fila' => 'Atendido']);
                
            // 4. Se veio de agendamento, marca o horário como concluído
            if ($atendimento->agendamento_id) {
                Agendamento::where('id', $atendimento->agendamento_id)->update(['status' => 'Concluido']);
            }
        });

        return redirect()->route('atendimento.index')
            ->with('success', 'Atendimento finalizado e histórico clínico consolidado com sucesso!');
    }

    /**
     * Salva via AJAX o dente clicado no Odontograma da View.
     */
    public function salvarProcedimentoDente(Request $request)
    {
        $request->validate([
            'numero_dente' => 'required|integer',
            'procedimento_id' => 'required|exists:procedimentos,id',
            'observacao' => 'nullable|string'
        ]);

        // Cria o registro na tabela que definimos no passo anterior
        $procedimentoDente = ProcedimentoDente::create([
            'numero_dente' => $request->numero_dente,
            'procedimento_id' => $request->procedimento_id,
            'observacao' => $request->observacao
        ]);

        // Retorna o ID criado para o JavaScript colocar no input oculto 'procedimento_dente_id'
        return response()->json([
            'success' => true, 
            'id' => $procedimentoDente->id, 
            'message' => 'Procedimento do dente mapeado!'
        ]);
    }

    /**
     * Exibe os detalhes de um atendimento específico (Modal ou Auditoria).
     */
    public function show(Atendimento $atendimento)
    {
        $atendimento->load(['paciente', 'dentista', 'prontuario']);
        return response()->json($atendimento);
    }

    /**
     * Remove ou cancela um registro de atendimento.
     */
    public function destroy(Atendimento $atendimento)
    {
        if ($atendimento->status_atendimento === 'Finalizado') {
            return redirect()->back()->with('error', 'Não é permitido excluir um atendimento já finalizado por motivos de histórico legal.');
        }

        DB::transaction(function () use ($atendimento) {
            if ($atendimento->agendamento_id) {
                Agendamento::where('id', $atendimento->agendamento_id)->update(['status' => 'Agendado']);
            }
            $atendimento->delete();
        });

        return redirect()->route('atendimento.index')->with('success', 'Registro de atendimento removido.');
    }
}