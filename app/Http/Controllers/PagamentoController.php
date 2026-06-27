<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\OrcamentoPaciente;
use App\Models\Paciente;
use App\Models\FormaPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagamentoController extends Controller
{
    /**
     * Exibe a listagem de pagamentos (Fluxo de Caixa) com filtros por data.
     */
    public function index(Request $request)
    {
        $query = Pagamento::with(['paciente', 'orcamento', 'formaPagamento']);

        // Filtro por período (Padrão: registros de hoje se nenhum intervalo for passado)
        $dataInicio = $request->get('data_inicio', today()->toDateString());
        $dataFim = $request->get('data_fim', today()->toDateString());

        $query->whereBetween('data_pagamento', [$dataInicio . ' 00:00:00', $dataFim . ' 23:59:59']);

        // Filtro opcional por Paciente
        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        // Filtro opcional por Status do Pagamento (Pago, Pendente, Estornado)
        if ($request->has('status_pagamento')) {
            $query->where('status_pagamento', $request->status_pagamento);
        }

        $pagamentos = $query->orderBy('data_pagamento', 'desc')->paginate(15);

        return response()->json($pagamentos);
        // Se usar Blade: return view('financeiro.pagamentos.index', compact('pagamentos'));
    }

    /**
     * Auxilia a recepção trazendo os seletores para registrar um recebimento.
     */
    public function create()
    {
        $formasPagamento = FormaPagamento::where('ativo', true)->orderBy('nome')->get();
        return response()->json(compact('formasPagamento'));
    }

    /**
     * Registra a entrada de um novo pagamento no sistema.
     */
    public function store(Request $request)
    {
        // 1. Validação rigorosa dos dados financeiros de entrada
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'orcamento_id' => 'required|exists:orcamento_pacientes,id',
            'forma_pagamento_id' => 'required|exists:forma_pagamentos,id',
            'valor_pago' => 'required|numeric|min:0.01',
            'status_pagamento' => 'required|string|in:Pendente,Pago',
            'observacoes' => 'nullable|string|max:255'
        ]);

        // 2. Regra de Negócio: Impede que o pagamento ultrapasse o saldo devedor do orçamento
        $orcamento = OrcamentoPaciente::find($request->orcamento_id);

        // Calcula quanto já foi pago neste orçamento anteriormente (apenas considerando status 'Pago')
        $totalJaPago = Pagamento::where('orcamento_id', $orcamento->id)
            ->where('status_pagamento', 'Pago')
            ->sum('valor_pago');

        $saldoDevedor = $orcamento->valor_liquido - $totalJaPago;

        if ($request->status_pagamento === 'Pago' && $request->valor_pago > $saldoDevedor) {
            return response()->json([
                'error' => 'O valor informado (R$ ' . number_format($request->valor_pago, 2, ',', '.') . ') é maior do que o saldo devedor restante deste orçamento (R$ ' . number_format($saldoDevedor, 2, ',', '.') . ').'
            ], 422);
        }

        // 3. Processamento atômico do pagamento
        $pagamento = DB::transaction(function () use ($request, $orcamento, $saldoDevedor) {

            $pagamento = Pagamento::create([
                'paciente_id' => $request->paciente_id,
                'orcamento_id' => $request->orcamento_id,
                'forma_pagamento_id' => $request->forma_pagamento_id,
                'valor_pago' => $request->valor_pago,
                'data_pagamento' => now(),
                'status_pagamento' => $request->status_pagamento,
                'observacoes' => $request->observacoes
            ]);

            // Se o valor pago liquidar o saldo devedor exato, altera o status do orçamento pai para 'Finalizado'
            if ($request->status_pagamento === 'Pago' && bcsub($saldoDevedor, $request->valor_pago, 2) <= 0) {
                $orcamento->update(['status' => 'Finalizado']);
            }

            return $pagamento;
        });

        return response()->json([
            'message' => 'Pagamento registrado com sucesso!',
            'data' => $pagamento
        ], 201);
    }

    /**
     * Exibe o recibo/detalhes de uma transação específica.
     */
    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['paciente', 'orcamento', 'formaPagamento']);
        return response()->json($pagamento);
    }

    /**
     * Retorna os dados para edição do pagamento.
     */
    public function edit(Pagamento $pagamento)
    {
        return response()->json($pagamento);
    }

    /**
     * Permite atualizar observações ou confirmar um pagamento que estava 'Pendente'.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        // Se o pagamento já foi efetuado ou estornado, impede alterações de valores retroativos
        if ($pagamento->status_pagamento === 'Estornado') {
            return response()->json(['error' => 'Não é permitido modificar um registro financeiro que foi estornado.'], 422);
        }

        $request->validate([
            'status_pagamento' => 'required|string|in:Pendente,Pago,Estornado',
            'observacoes' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request, $pagamento) {

            // Se o status mudou para 'Estornado', precisamos reavaliar o status do orçamento pai
            if ($request->status_pagamento === 'Estornado' && $pagamento->status_pagamento === 'Pago') {
                $orcamento = $pagamento->orcamento;
                if ($orcamento->status === 'Finalizado') {
                    // Retorna o orçamento para 'Aprovado' (Ativo mas com saldo pendente de quitação)
                    $orcamento->update(['status' => 'Aprovado']);
                }
            }

            $pagamento->update([
                'status_pagamento' => $request->status_pagamento,
                'observacoes' => $request->observacoes
            ]);
        });

        return response()->json([
            'message' => 'Registro de pagamento atualizado com sucesso!'
        ]);
    }

    /**
     * Remove ou realiza o estorno do pagamento no caixa da clínica.
     */
    public function destroy(Pagamento $pagamento)
    {
        // Regra de Auditoria Fiscal: Registros de pagamentos efetivados ('Pago') não devem ser apagados fisicamente 
        // do banco de dados para evitar fraudes no fluxo de caixa. Em vez disso, altera-se o status para 'Estornado'.
        if ($pagamento->status_pagamento === 'Pago') {
            DB::transaction(function () use ($pagamento) {
                $pagamento->update([
                    'status_pagamento' => 'Estornado',
                    'observacoes' => '[ESTORNO AUTOMÁTICO] Registro estornado em ' . now()->format('d/m/Y H:i')
                ]);

                // Ajusta o orçamento pai se ele estivesse concluído
                $orcamento = $pagamento->orcamento;
                if ($orcamento && $orcamento->status === 'Finalizado') {
                    $orcamento->update(['status' => 'Aprovado']);
                }
            });

            return response()->json([
                'message' => 'O pagamento não foi excluído fisicamente por segurança fiscal. Seu status foi atualizado para Estornado.'
            ], 200);
        }

        // Se o pagamento estivesse meramente 'Pendente' (Sem entrada de dinheiro real), a exclusão física é permitida
        $pagamento->delete();

        return response()->json(['message' => 'Registro de pagamento pendente excluído com sucesso.']);
    }
}