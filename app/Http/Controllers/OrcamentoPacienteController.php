<?php

namespace App\Http\Controllers;

use App\Models\OrcamentoPaciente;
use App\Models\OrcamentoItem;
use App\Models\Paciente;
use App\Models\Dentista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrcamentoPacienteController extends Controller
{
    /**
     * Exibe a listagem de orçamentos (com filtros e paginação).
     */
    public function index(Request $request)
    {
        $query = OrcamentoPaciente::with(['paciente', 'dente_responsavel']);

        // Filtrar por Paciente específico
        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        // Filtrar por Status (Pendente, Aprovado, Recusado, Finalizado)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orcamentos = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($orcamentos);
    }

    /**
     * Auxilia o carregamento de dados para criação de um orçamento.
     */
    public function create()
    {
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();

        return response()->json(compact('pacientes', 'dentistas'));
    }

    /**
     * Grava um novo orçamento e todos os seus itens associados.
     */
    public function store(Request $request)
    {
        // 1. Validação do Orçamento Pai e da estrutura dos itens dependentes
        $request->validate([
            'paciente_id'     => 'required|exists:pacientes,id',
            'dentista_id'     => 'required|exists:dentistas,id',
            'desconto_global' => 'required|numeric|min:0',
            'observacoes'     => 'nullable|string',
            'itens'           => 'required|array|min:1',
            'itens.*.procedimento_id' => 'required|exists:procedimentos,id',
            'itens.*.quantidade'      => 'required|integer|min:1',
            'itens.*.valor_unitario'  => 'required|numeric|min:0',
            'itens.*.desconto'        => 'required|numeric|min:0',
            'itens.*.dente_regiao'    => 'nullable|string|max:50'
        ]);

        // 2. Uso de Transaction para garantir consistência financeira integral
        $orcamento = DB::transaction(function () use ($request) {
            
            // Cria o registro pai inicial
            $orcamento = OrcamentoPaciente::create([
                'paciente_id'     => $request->paciente_id,
                'dentista_id'     => $request->dentista_id,
                'valor_bruto'     => 0, // Será recalculado abaixo
                'desconto_global' => $request->desconto_global,
                'valor_liquido'   => 0, // Será recalculado abaixo
                'status'          => 'Pendente',
                'observacoes'     => $request->observacoes
            ]);

            $valorBrutoTotal = 0;

            // Percorre e grava os itens do orçamento
            foreach ($request->itens as $item) {
                $valorTotalItem = ($item['quantidade'] * $item['valor_unitario']) - $item['desconto'];
                $valorBrutoTotal += ($item['quantidade'] * $item['valor_unitario']);

                OrcamentoItem::create([
                    'orcamento_id'    => $orcamento->id,
                    'procedimento_id' => $item['procedimento_id'],
                    'quantidade'      => $item['quantidade'],
                    'valor_unitario'  => $item['valor_unitario'],
                    'desconto'        => $item['desconto'],
                    'valor_total'     => $valorTotalItem,
                    'dente_regiao'    => $item['dente_regiao'] ?? null
                ]);
            }

            // Atualiza os valores finais consolidados do Pai
            $valorLiquidoFinal = $valorBrutoTotal - $request->desconto_global;
            
            $orcamento->update([
                'valor_bruto'   => $valorBrutoTotal,
                'valor_liquido' => max(0, $valorLiquidoFinal) // Garante que o valor líquido não seja negativo
            ]);

            return $orcamento;
        });

        return response()->json([
            'message' => 'Orçamento e plano de tratamento gerados com sucesso!',
            'data' => $orcamento->load('itens')
        ], 201);
    }

    /**
     * Exibe os detalhes de um orçamento com todos os itens e nomes de procedimentos inclusos.
     */
    public function show(OrcamentoPaciente $orcamentoPaciente)
    {
        // Aplica Eager Loading aninhado: carrega os itens e, para cada item, o procedimento correspondente
        $orcamentoPaciente->load(['paciente', 'dente_responsavel', 'itens.procedimento']);
        
        return response()->json($orcamentoPaciente);
    }

    /**
     * Retorna os dados para edição do orçamento.
     */
    public function edit(OrcamentoPaciente $orcamentoPaciente)
    {
        $orcamentoPaciente->load('itens');
        return response()->json($orcamentoPaciente);
    }

    /**
     * Atualiza as informações gerais e sincroniza as linhas de itens do orçamento.
     */
    public function update(Request $request, OrcamentoPaciente $orcamentoPaciente)
    {
        // Impede alterar orçamentos que já foram fechados ou faturados por integridade financeira
        if (in_array($orcamentoPaciente->status, ['Aprovado', 'Finalizado'])) {
            return response()->json([
                'error' => 'Não é permitido modificar orçamentos que já foram Aprovados ou Finalizados.'
            ], 422);
        }

        $request->validate([
            'status'          => 'required|string|in:Pendente,Aprovado,Recusado,Cancelado',
            'desconto_global' => 'required|numeric|min:0',
            'observacoes'     => 'nullable|string',
            'itens'           => 'required|array|min:1',
            'itens.*.procedimento_id' => 'required|exists:procedimentos,id',
            'itens.*.quantidade'      => 'required|integer|min:1',
            'itens.*.valor_unitario'  => 'required|numeric|min:0',
            'itens.*.desconto'        => 'required|numeric|min:0',
            'itens.*.dente_regiao'    => 'nullable|string|max:50'
        ]);

        DB::transaction(function () use ($request, $orcamentoPaciente) {
            
            // 1. Remove os itens antigos para evitar duplicidade ou órfãos antes de regravar os novos
            $orcamentoPaciente->itens()->delete();

            $valorBrutoTotal = 0;

            // 2. Recadastra as linhas atualizadas enviadas pelo front-end
            foreach ($request->itens as $item) {
                $valorTotalItem = ($item['quantidade'] * $item['valor_unitario']) - $item['desconto'];
                $valorBrutoTotal += ($item['quantidade'] * $item['valor_unitario']);

                OrcamentoItem::create([
                    'orcamento_id'    => $orcamentoPaciente->id,
                    'procedimento_id' => $item['procedimento_id'],
                    'quantidade'      => $item['quantidade'],
                    'valor_unitario'  => $item['valor_unitario'],
                    'desconto'        => $item['desconto'],
                    'valor_total'     => $valorTotalItem,
                    'dente_regiao'    => $item['dente_regiao'] ?? null
                ]);
            }

            // 3. Atualiza os metadados financeiros consolidados do pai
            $valorLiquidoFinal = $valorBrutoTotal - $request->desconto_global;

            $orcamentoPaciente->update([
                'status'          => $request->status,
                'desconto_global' => $request->desconto_global,
                'valor_bruto'     => $valorBrutoTotal,
                'valor_liquido'   => max(0, $valorLiquidoFinal),
                'observacoes'     => $request->observacoes
            ]);
        ]);

        return response()->json([
            'message' => 'Orçamento atualizado e recalculado com sucesso!'
        ]);
    }

    /**
     * Remove ou descarta o plano de orçamento.
     */
    public function destroy(OrcamentoPaciente $orcamentoPaciente)
    {
        // Segurança: Bloqueia a exclusão se houver qualquer vínculo de faturamento aprovado
        if ($orcamentoPaciente->status === 'Aprovado') {
            return response()->json([
                'error' => 'Orçamentos aprovados fazem parte da contabilidade ativa e não podem ser excluídos.'
            ], 422);
        }

        DB::transaction(function () use ($orcamentoPaciente) {
            // Remove os itens filhos antes para respeitar a integridade das chaves estrangeiras
            $orcamentoPaciente->itens()->delete();
            $orcamentoPaciente->delete();
        });

        return response()->json(['message' => 'Orçamento removido com sucesso do sistema.']);
    }
}