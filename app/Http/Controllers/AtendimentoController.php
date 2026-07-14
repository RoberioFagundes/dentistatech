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
use App\Models\OrcamentoPaciente;
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
    public function create(Request $request)
    {
        $userId = auth()->id();
        $dentista = Dentista::where('user_id', $userId)->first();

        $agendamentos = $dentista
            ? Agendamento::where('status', 'AGENDADO')
            ->where('dentista_id', $dentista->id)
            ->with('paciente') // Carrega os dados do paciente de uma vez
            ->get(['id', 'paciente_id', 'data']) // Apenas campos que existem na tabela
            : collect();

        $pacientes = Paciente::orderBy('nome')->get(['id', 'nome']);
        $procedimentoDentes = Procedimento::all();
        $dentistas = Dentista::orderBy('nome')->get(['id', 'nome']);

        // Pega os parâmetros da URL
        $pacienteId = $request->paciente_id;
        $agendamentoId = $request->agendamento_id;

        return view('dentista.atendimento.formulario', compact(
            'agendamentos',
            'pacientes',
            'procedimentoDentes',
            'dentistas',
            'pacienteId',
            'agendamentoId'
        ));
    }

    /**
     * Inicia oficialmente o atendimento clínico (Salva o rascunho/abertura).
     */
    public function store(Request $request)
    {
        try {
            // 1. Captura os dados
            $dados = $request->all();
            $temProcedimentosValidos = false;

            if (!empty($dados['procedimentos']) && is_array($dados['procedimentos'])) {
                // ✅ FILTRA PELO NOME CORRETO: procedimento_id (não id_procedimento)
                $dados['procedimentos'] = array_values(array_filter(
                    $dados['procedimentos'],
                    fn($item) => !empty($item['procedimento_id'])
                ));
                $temProcedimentosValidos = count($dados['procedimentos']) > 0;
            }

            $request->merge($dados);

            // 2. VALIDAÇÃO CORRIGIDA: usa procedimento_id
            $request->validate([
                'dentista_id' => 'required|exists:dentistas,id',
                'paciente_id' => 'required|exists:pacientes,id',
                'agendamento_id' => 'nullable|exists:agendamentos,id',
                'queixa_principal' => 'required|string|max:255',
                'diagnostico' => 'nullable|string',
                'tratamento_executado' => 'nullable|string',
                'observacoes_clinicas' => 'nullable|string',
                'validade' => 'nullable|date',
                'total_orcamento' => 'nullable|numeric',
                'desconto' => 'nullable|numeric',
                'procedimentos' => 'nullable|array',
                // ✅ TROQUEI id_procedimento POR procedimento_id
                'procedimentos.*.procedimento_id' => 'required|exists:procedimentos,id',
                'procedimentos.*.dente' => 'required|string',
                'procedimentos.*.valor' => 'required|numeric',
                'procedimentos.*.observacao' => 'nullable|string',
            ]);

            // 3. Transação
            return DB::transaction(function () use ($request, $dados, $temProcedimentosValidos) {

                $atendimento = Atendimento::create($request->except([
                    'procedimentos',
                    '_token',
                    '_method',
                    'validade',
                    'total_orcamento',
                    'desconto'
                ]));

                if ($temProcedimentosValidos) {
                    foreach ($dados['procedimentos'] as $item) {
                        // ✅ BUSCA PELO NOME CORRETO
                        $procedimento = \App\Models\Procedimento::findOrFail($item['procedimento_id']);
                        $valorUnitario = $item['valor'] ?? $procedimento->valor;

                        // Tabela pivô
                        $atendimento->procedimentos()->attach($item['procedimento_id'], [
                            'dente' => $item['dente'],
                            'valor' => $valorUnitario,
                            'observacao' => $item['observacao'] ?? null,
                            'user_id' => auth()->id(),
                        ]);

                        // ProcedimentoDente
                        $procDente = \App\Models\ProcedimentoDente::create([
                            'atendimento_id' => $atendimento->id,
                            'numero_dente' => $item['dente'],
                            'procedimento_id' => $item['procedimento_id'],
                            'observacao' => $item['observacao'] ?? null,
                            'valor' => $valorUnitario,
                            'user_id' => auth()->id(),
                        ]);

                        // Orçamento
                        $orcamento = OrcamentoPaciente::create([
                            'paciente_id' => $request->paciente_id,
                            'user_id' => auth()->id(),
                            'validade' => $request->validade,
                            'valor' => $valorUnitario,
                            'total_orcamento' => $request->total_orcamento,
                            'desconto' => $request->desconto,
                            'valor_liquido' => $request->total_orcamento - ($request->desconto ?? 0),
                            'atendimento_id' => $atendimento->id,
                            'procedimento_dentes_id' => $procDente->id,
                            'procedimento_id' => $item['procedimento_id'],
                        ]);

                        // Itens do orçamento
                        \DB::table('orcamento_items')->insert([
                            'orcamento_paciente_id' => $orcamento->id,
                            'procedimento_dente_id' => $procDente->id,
                            'dente' => $item['dente'],
                            'face' => $item['face'] ?? null,
                            'quantidade' => $item['quantidade'] ?? 1,
                            'valor' => $valorUnitario,
                            'status' => 'ativo',
                            'user_id' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                return redirect()->route('atendimento.index')
                    ->with('success', 'Salvo com sucesso! Procedimentos: ' . ($temProcedimentosValidos ? count($dados['procedimentos']) . ' registrados' : 'nenhum informado'));
            });
        } catch (\Throwable $e) {
            dd('Erro: ' . $e->getMessage(), 'Linha: ' . $e->getLine());
        }
    }

    /**
     * Carrega a tela com os dados para o dentista editar ou finalizar a consulta.
     */
    public function edit($id)
    {
        // 1. Usar findOrFail com tratamento personalizado (mais limpo)
        // Ou mantenha a sua verificação — ambas são válidas
        try {
            $atendimento = Atendimento::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('atendimento.index')
                ->with('error', 'Atendimento não encontrado.');
        }

        $userId = auth()->id();
        $dentista = Dentista::where('user_id', $userId)->first();

        // 2. Carregar relacionamentos com Eager Loading (evita consultas duplicadas)
        // Se o atendimento tem relacionamentos como 'procedimentos', 'paciente' etc, carregue aqui
        $atendimento->load(['procedimentos', 'paciente']); // Ajuste conforme seus relacionamentos

        // 3. Consulta de agendamentos com otimização
        $agendamentos = $dentista
            ? Agendamento::where('status', 'AGENDADO')
            ->where('dentista_id', $dentista->id)
            ->get(['id', 'paciente_id', 'data', 'hora_inicio', 'hora_fim'])
            : collect();

        // 4. Garantir consistência e campos necessários
        $pacientes = Paciente::orderBy('nome')->get(['id', 'nome']);
        $procedimentoDentes = Procedimento::orderBy('nome')->get(['id', 'nome', 'valor']); // Adicione ordenação e campos úteis
        $dentistas = Dentista::orderBy('nome')->get(['id', 'nome']);

        // 5. Validação de permissão (opcional mas recomendado)
        // Verifica se o dentista tem permissão para editar este atendimento
        if ($dentista && $atendimento->dentista_id !== $dentista->id) {
            abort(403, 'Você não tem permissão para editar este atendimento.');
        }

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
    public function destroy($id)
    {
        // 1. Primeiro, deleta os agendamentos vinculados
        \App\Models\Agendamento::where('atendimento_id', $id)->delete();

        // 2. Depois, deleta o atendimento
        \App\Models\Atendimento::destroy($id);

        return redirect()->back()->with('success', 'Atendimento e agendamentos removidos com sucesso!');
    }
}
