<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Agendamento;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\FilaAtendimento;
use App\Models\Prontuario;
use App\Models\ProcedimentoDente;
use App\Models\Procedimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AtendimentoController extends Controller implements HasMiddleware
{
    /**
     * Exibe o histórico de atendimentos realizados na clínica.
     */
    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    public function index(Request $request)
    {
        $query = Atendimento::with([
            'paciente',
            'dentista',
            'agendamento',
        ])->where('user_id', auth()->id());

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->filled('dentista_id')) {
            $query->where('dentista_id', $request->dentista_id);
        }

        $atendimentos = $query->orderBy('created_at', 'desc')->paginate(15);

        $pacientes = Paciente::orderBy('nome')->get();
        $dentistas = Dentista::orderBy('nome')->get();

        return view('dentista.atendimento.index', compact('atendimentos', 'pacientes', 'dentistas'));
    }

    /**
     * Tela para abertura manual de atendimento.
     */
    public function create()
    {
        // 1. Pega o ID do usuário que está logado agora
        $userId = auth()->id();

        // 2. Busca na tabela de dentistas quem é o dentista que tem esse user_id
        $dentista = Dentista::where('user_id', $userId)->first();

        // 3. Verifica se encontrou um dentista vinculado a este usuário
        if ($dentista) {
            // Filtra apenas os agendamentos que pertencem a este dentista
            $agendamentos = Agendamento::where('status', 'AGENDADO')
                ->where('dentista_id', $dentista->id)
                ->orderBy('data', 'asc')
                ->get();
        } else {
            // Caso o usuário logado não seja um dentista, deixa vazio
            $agendamentos = collect();
        }

        $pacientes = Paciente::orderBy('nome')->get();
        $procedimentoDentes = Procedimento::all();
        $dentistas = Dentista::orderBy('nome')->get();

        return view('dentista.atendimento.formulario', compact('pacientes', 'agendamentos', 'procedimentoDentes', 'dentistas'));
    }

    /**
     * Inicia oficialmente o atendimento clínico (Salva o rascunho/abertura).
     */
    public function store(Request $request)
    {
        // 1. Validação (opcional, mas recomendada)
        $request->validate([
            'procedimentos' => 'required|array',
            'procedimentos.*.id_procedimento' => 'required',
            'procedimentos.*.dente' => 'required',
        ]);

        // 2. Salva o Atendimento (exemplo)
        $atendimento = Atendimento::create($request->except(['procedimentos', '_token']));

        // 3. Salva os procedimentos na tabela 'procedimento_dentes'
        if ($request->has('procedimentos')) {
            foreach ($request->input('procedimentos') as $item) {
                \DB::table('procedimento_dentes')->insert([
                    'atendimento_id' => $atendimento->id,
                    'numero_dente' => $item['dente'],
                    'procedimento_id' => $item['id_procedimento'],
                    'observacao' => $item['observacao'] ?? null,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('atendimento.index')->with('success', 'Atendimento e procedimentos salvos com sucesso!');
    }

    /**
     * Carrega a tela com os dados para o dentista editar ou finalizar a consulta.
     */
    public function edit(Atendimento $atendimento)
    {
        $userId = auth()->id();
        $dentista = Dentista::where('user_id', $userId)->first();

        $agendamentos = $dentista 
            ? Agendamento::where('status', 'AGENDADO')->where('dentista_id', $dentista->id)->get()
            : collect();

        // Carregue os dados necessários para o formulário
        $pacientes = Paciente::orderBy('nome')->get();
        $procedimentoDentes = Procedimento::all();
        $dentistas = Dentista::orderBy('nome')->get();

        return view('dentista.atendimento.formulario', compact(
            'atendimento', 
            'pacientes', 
            'agendamentos', 
            'procedimentoDentes', 
            'dentistas'
        ));
    }

    /**
     * Salva as alterações intermediárias do atendimento (Salvar Rascunho).
     */
    public function update(Request $request, Atendimento $atendimento)
    {
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'agendamento_id' => 'nullable|exists:agendamentos,id',
            'procedimento_dente_id' => 'nullable|exists:procedimento_dentes,id',
            'queixa_principal' => 'required|string|max:255',
            'diagnostico' => 'nullable|string',
            'tratamento_executado' => 'nullable|string',
            'observacoes_clinicas' => 'nullable|string',
        ]);

        $dadosValidados['user_id'] = auth()->id();

        $atendimento->update($dadosValidados);

        return redirect()->route('atendimento.index')->with('success', 'Alterações do atendimento salvas com sucesso!');
    }

    /**
     * REGRA DE NEGÓCIO SEPARADA: Finaliza o atendimento de vez e gera o prontuário.
     * Mapear esta função para uma rota específica, ex: Route::post('/atendimento/{atendimento}/finalizar', ...)
     */
    public function finalizar(Request $request, Atendimento $atendimento)
    {
        $request->validate([
            'status_atendimento' => 'required|string|in:Finalizado,Suspenso',
            'evolucao_clinica' => 'required|string|min:10',
            'prescricao_medica' => 'nullable|string',
            'observacoes_finais' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $atendimento) {
            // 1. Atualiza o status do Atendimento definitivo
            $atendimento->update([
                'status_atendimento' => $request->status_atendimento,
                'observacoes_clinicas' => $request->observacoes_finais,
                'hora_fim' => now(),
            ]);

            // 2. Cria o Prontuário clínico permanente
            Prontuario::create([
                'paciente_id' => $atendimento->paciente_id,
                'dentista_id' => $atendimento->dentista_id,
                'atendimento_id' => $atendimento->id,
                'descricao' => $request->evolucao_clinica,
                'prescricao' => $request->prescricao_medica,
                'data_registro' => now(),
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

        return redirect()->route('atendimento.index')->with('success', 'Atendimento finalizado e histórico clínico consolidado com sucesso!');
    }

    /**
     * Salva via AJAX o dente clicado no Odontograma da View.
     */
    public function salvarProcedimentoDente(Request $request)
    {
        $request->validate([
            'numero_dente' => 'required|integer',
            'procedimento_id' => 'required|exists:procedimentos,id',
            'observacao' => 'nullable|string',
        ]);

        // Cria o registro na tabela que definimos
        $procedimentoDente = ProcedimentoDente::create([
            'numero_dente' => $request->numero_dente,
            'procedimento_id' => $request->procedimento_id,
            'observacao' => $request->observacao,
        ]);

        // Retorna o ID criado para o JavaScript colocar no input oculto 'procedimento_dente_id'
        return response()->json([
            'success' => true,
            'id' => $procedimentoDente->id,
            'message' => 'Procedimento do dente mapeado!',
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