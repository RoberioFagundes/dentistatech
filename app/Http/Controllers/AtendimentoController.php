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
use Barryvdh\DomPDF\Facade\Pdf;

class AtendimentoController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function gerarPdf(Atendimento $atendimento)
    {
        $atendimento->load([
            'procedimentos' => fn($q) => $q->withPivot('numero_dente', 'observacao'),
            'paciente',
            'dentista',
            'agendamento',
            'orcamento'
        ]);

        $pdf = Pdf::loadView('dentista.atendimento.formulario-pdf', compact('atendimento'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('atendimento-' . $atendimento->id . '.pdf');
    }

    public function index(Request $request)
    {
        $query = Atendimento::with(['paciente', 'dentista', 'agendamento'])
            ->where('user_id', auth()->id());

        if ($request->filled('paciente_id')) $query->where('paciente_id', $request->paciente_id);
        if ($request->filled('dentista_id')) $query->where('dentista_id', $request->dentista_id);

        $atendimentos = $query->orderByDesc('created_at')->paginate(15);
        $pacientes = Paciente::orderBy('nome')->get(['id', 'nome']);
        $dentistas = Dentista::orderBy('nome')->get(['id', 'nome']);

        return view('dentista.atendimento.index', compact('atendimentos', 'pacientes', 'dentistas'));
    }

    public function create(Request $request)
    {
        $usuarioLogadoId = auth()->id();

        $dentista = Dentista::where('user_id', $usuarioLogadoId)->first();

        $agendamentos = $dentista
            ? Agendamento::where('status', 'AGENDADO')
            ->where('dentista_id', $dentista->id)
            ->with('paciente')
            ->get(['id', 'paciente_id', 'data', 'hora_inicio', 'hora_fim'])
            : collect();

        // ✅ FORMA SEGURA: SEM DEPENDER DE RELACIONAMENTO NO PACIENTE
        $idsAgendamentos = Agendamento::where('dentista_id', $dentista?->id)
            ->pluck('paciente_id');
        $idsAtendimentos = Atendimento::where('user_id', $usuarioLogadoId)
            ->pluck('paciente_id');

        $pacientes = Paciente::where('user_id', $usuarioLogadoId)
            ->orWhereIn('id', $idsAgendamentos)
            ->orWhereIn('id', $idsAtendimentos)
            ->distinct()
            ->orderBy('nome')
            ->get(['id', 'nome', 'sobrenome']);

        $procedimentoDentes = Procedimento::where('ativo', 1)
            ->where('user_id', $usuarioLogadoId)
            ->distinct()
            ->orderBy('nome')
            ->get(['id', 'nome', 'valor']);

        $dentistas = Dentista::where('user_id', $usuarioLogadoId)
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('dentista.atendimento.formulario', [
            'agendamentos' => $agendamentos,
            'pacientes' => $pacientes,
            'procedimentoDentes' => $procedimentoDentes,
            'dentistas' => $dentistas,
            'pacienteId' => $request->paciente_id,
            'agendamentoId' => $request->agendamento_id
        ]);
    }

    public function edit($id)
    {
        try {
            $atendimento = Atendimento::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('atendimento.index')->with('error', 'Atendimento não encontrado.');
        }

        $dentista = Dentista::where('user_id', auth()->id())->first();

        $atendimento->load([
            'procedimentos' => fn($q) => $q->distinct()->withPivot('numero_dente', 'observacao'),
            'paciente'
        ]);

        $agendamentos = $dentista
            ? Agendamento::where('status', 'AGENDADO')->where('dentista_id', $dentista->id)->get(['id', 'paciente_id', 'data', 'hora_inicio', 'hora_fim'])
            : collect();

        $pacientes = Paciente::orderBy('nome')->get(['id', 'nome']);

        // ✅ Mesmo filtro: ativos + sem repetição
        $procedimentoDentes = Procedimento::where('ativo', 1)
            ->where('user_id', auth()->id()) // ✅ SÓ OS DESTE USUÁRIO
            ->distinct()
            ->orderBy('nome')
            ->get(['id', 'nome', 'valor']);

        $dentistas = Dentista::orderBy('nome')->get(['id', 'nome']);

        if ($dentista && $atendimento->dentista_id !== $dentista->id) abort(403, 'Sem permissão para editar.');

        return view('dentista.atendimento.formulario', compact('atendimento', 'pacientes', 'agendamentos', 'procedimentoDentes', 'dentistas'));
    }

    public function store(Request $request)
    {
        try {
            $dados = $request->all();
            $temProcedimentosValidos = false;

            if (!empty($dados['procedimentos']) && is_array($dados['procedimentos'])) {
                $dados['procedimentos'] = array_values(array_filter(
                    $dados['procedimentos'],
                    fn($item) => !empty($item['procedimento_id'])
                ));
                $temProcedimentosValidos = count($dados['procedimentos']) > 0;
            }

            $request->merge($dados);

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
                'procedimentos.*.procedimento_id' => 'required|exists:procedimentos,id',
                'procedimentos.*.dente' => 'required|string',
                'procedimentos.*.valor' => 'required|numeric',
                'procedimentos.*.observacao' => 'nullable|string',
            ]);

            return DB::transaction(function () use ($request, $dados, $temProcedimentosValidos) {

                $atendimento = Atendimento::create($request->except([
                    'procedimentos',
                    '_token',
                    '_method',
                    'validade',
                    'total_orcamento',
                    'desconto'
                ]));

                if ($request->agendamento_id) {
                    DB::table('fila_atendimentos')
                        ->where('agendamento_id', $request->agendamento_id)
                        ->update([
                            'status_fila' => 'FINALIZADO',
                            'chamado_em' => now(),
                            'user_id' => auth()->id(),
                            'updated_at' => now()
                        ]);
                }

                if ($temProcedimentosValidos) {
                    foreach ($dados['procedimentos'] as $item) {
                        $procedimento = Procedimento::findOrFail($item['procedimento_id']);
                        $valorUnitario = $item['valor'] ?? $procedimento->valor;
                        $totalOrcamento = $request->total_orcamento ?? $valorUnitario;
                        $desconto = $request->desconto ?? 0;
                        $valorLiquido = $totalOrcamento - $desconto;

                        $procDente = ProcedimentoDente::create([
                            'atendimento_id' => $atendimento->id,
                            'numero_dente' => $item['dente'],
                            'procedimento_id' => $item['procedimento_id'],
                            'observacao' => $item['observacao'] ?? null,
                            'user_id' => auth()->id(),
                        ]);

                        // ✅ CORREÇÃO: ATRIBUI A VARIÁVEL PARA USAR DEPOIS
                        $orcamento = OrcamentoPaciente::create([
                            'paciente_id' => $request->paciente_id,
                            'user_id' => auth()->id(),
                            'validade' => $request->validade,
                            'valor' => $valorUnitario,
                            'total_orcamento' => $totalOrcamento,
                            'desconto' => $desconto,
                            'valor_liquido' => $valorLiquido,
                            'saldo_restante' => $valorLiquido,
                            'atendimento_id' => $atendimento->id,
                            'procedimento_dentes_id' => $procDente->id,
                            'procedimento_id' => $item['procedimento_id'],
                        ]);

                        DB::table('orcamento_items')->insert([
                            'orcamento_paciente_id' => $orcamento->id, // AGORA EXISTE!
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

                if ($request->agendamento_id) {
                    Agendamento::where('id', $request->agendamento_id)->update(['status' => 'ATENDIDO']);
                }

                return redirect()->route('atendimento.index')
                    ->with('success', 'Salvo! Procedimentos: ' . ($temProcedimentosValidos ? count($dados['procedimentos']) . ' registrados' : 'nenhum') . ' — orçamento gerado com sucesso!');
            });
        } catch (\Throwable $e) {
            dd('Erro: ' . $e->getMessage(), 'Linha: ' . $e->getLine());
        }
    }

    public function update(Request $request, Atendimento $atendimento)
    {
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'agendamento_id' => 'nullable|exists:agendamentos,id',
            'queixa_principal' => 'required|string|max:255',
            'diagnostico' => 'nullable|string',
            'tratamento_executado' => 'nullable|string',
            'observacoes_clinicas' => 'nullable|string',
            'validade' => 'nullable|date',
            'total_orcamento' => 'nullable|numeric',
            'desconto' => 'nullable|numeric',
            'procedimentos' => 'nullable|array',
            'procedimentos.*.dente' => 'required|string',
            'procedimentos.*.procedimento_id' => 'required|exists:procedimentos,id',
            'procedimentos.*.observacao' => 'nullable|string',
            'procedimentos.*.valor' => 'nullable|numeric',
        ]);

        $dadosValidados['user_id'] = auth()->id();
        $atendimento->update($dadosValidados);

        if ($request->has('procedimentos')) {
            $dadosProcedimentos = [];
            foreach ($request->procedimentos as $proc) {
                $dadosProcedimentos[$proc['procedimento_id']] = [
                    'numero_dente' => $proc['dente'],
                    'observacao' => $proc['observacao'] ?? null,
                    'user_id' => auth()->id(),
                ];
            }
            $atendimento->procedimentos()->sync($dadosProcedimentos);
        } else {
            $atendimento->procedimentos()->detach();
        }

        $atendimento->orcamento()->updateOrCreate(
            ['atendimento_id' => $atendimento->id],
            [
                'validade' => $dadosValidados['validade'] ?? null,
                'total_orcamento' => $dadosValidados['total_orcamento'] ?? 0,
                'desconto' => $dadosValidados['desconto'] ?? 0,
            ]
        );

        return redirect()->route('atendimento.index')->with('success', 'Alterações salvas!');
    }

    public function finalizar(Request $request, Atendimento $atendimento)
    {
        $request->validate([
            'status_atendimento' => 'required|string|in:Finalizado,Suspenso',
            'evolucao_clinica' => 'required|string|min:10',
            'prescricao_medica' => 'nullable|string',
            'observacoes_finais' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $atendimento) {
            $atendimento->update([
                'status_atendimento' => $request->status_atendimento,
                'observacoes_clinicas' => $request->observacoes_finais,
                'hora_fim' => now(),
            ]);

            Prontuario::create([
                'paciente_id' => $atendimento->paciente_id,
                'dentista_id' => $atendimento->dentista_id,
                'atendimento_id' => $atendimento->id,
                'descricao' => $request->evolucao_clinica,
                'prescricao' => $request->prescricao_medica,
                'data_registro' => now(),
            ]);

            FilaAtendimento::where('paciente_id', $atendimento->paciente_id)
                ->where('status_fila', 'Em Atendimento')
                ->update(['status_fila' => 'Atendido']);

            if ($atendimento->agendamento_id) {
                Agendamento::where('id', $atendimento->agendamento_id)->update(['status' => 'Concluido']);
            }
        });

        return redirect()->route('atendimento.index')->with('success', 'Atendimento finalizado!');
    }

    public function salvarProcedimentoDente(Request $request)
    {
        $request->validate([
            'numero_dente' => 'required|integer',
            'procedimento_id' => 'required|exists:procedimentos,id',
            'observacao' => 'nullable|string',
        ]);

        $procedimentoDente = ProcedimentoDente::create([
            'numero_dente' => $request->numero_dente,
            'procedimento_id' => $request->procedimento_id,
            'observacao' => $request->observacao,
        ]);

        return response()->json([
            'success' => true,
            'id' => $procedimentoDente->id,
            'message' => 'Procedimento vinculado!',
        ]);
    }

    public function show(Atendimento $atendimento)
    {
        $atendimento->load(['paciente', 'dentista', 'prontuario']);
        return response()->json($atendimento);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            Agendamento::where('atendimento_id', $id)->update(['atendimento_id' => null, 'status' => 'AGENDADO']);
            Atendimento::destroy($id);
        });

        return redirect()->route('atendimento.index')->with('success', 'Atendimento removido!');
    }
}
