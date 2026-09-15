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
        $userId = auth()->id(); // ✅ Pega o ID do usuário logado

        // ✅ Filtra APENAS os pagamentos do dentista/administrador logado
        $query = Pagamento::with(['orcamentoPaciente.paciente', 'usuario'])
            ->where('user_id', $userId); // ou onde for a coluna de vínculo

        // ✅ Busca por nome ou CPF do paciente
        if ($request->filled('busca')) {
            $termo = $request->input('busca');
            $query->whereHas('orcamentoPaciente.paciente', function ($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                    ->orWhere('sobrenome', 'LIKE', "%{$termo}%")
                    ->orWhere('cpf', 'LIKE', "%{$termo}%");
            });
        }

        $pagamentos = $query->latest()->paginate(15);

        // ✅ Mantém o termo de busca na paginação
        $pagamentos->appends(['busca' => $termo ?? '']);

        return view('dentista.pagamento.index', compact('pagamentos'));
    }

    /**
     * Auxilia a recepção trazendo os seletores para registrar um recebimento.
     */
    public function create($orcamento)
    {
        $userId = auth()->id();

        // ✅ Busca o orçamento garantindo que pertence ao dentista logado
        $orcamento = OrcamentoPaciente::with('paciente')
            ->where('id', $orcamento)
            ->where('user_id', $userId)
            ->firstOrFail();

        $formasPagamento = FormaPagamento::where('ativo', true)->orderBy('nome')->get();

        return view('dentista.pagamento.formulario', compact('orcamento', 'formasPagamento'));
    }

    /**
     * função responsavel por criar o pagamento do responsavel 
     */
    public function pagamento_confirmacao($id)
    {
        $orcamento = OrcamentoPaciente::findOrFail($id);
        $formasPagamento = FormaPagamento::where('ativo', true)->orderBy('nome')->get();

        return view('dentista.pagamento.formulario', compact('orcamento', 'formasPagamento'));
    }

    /**
     * Função auxiliar: Recalcula saldo e status do orçamento.
     */


    /**
     * Confirma pagamento total de um orçamento.
     */
    public function confirmar(Request $request, $id)
    {
        $orcamento = OrcamentoPaciente::findOrFail($id);

        $dados = $request->validate([
            'forma_pagamento' => 'required|string|max:45',
            'observacao' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            Pagamento::create([
                'orcamento_paciente_id' => $orcamento->id,
                'valor_pago' => $orcamento->valor_total,
                'forma_pagamento' => $dados['forma_pagamento'],
                'status_pagamento' => 'PAGO',
                'data_pagamento' => now(),
                'observacao' => $dados['observacao'],
            ]);

            $this->atualizarSaldoOrcamento($orcamento);

            DB::commit();
            return redirect()->route('pagamento.index')
                ->with('sucesso', 'Pagamento confirmado e orçamento quitado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erro ao confirmar: ' . $e->getMessage());
        }
    }

    /**
     * Registra novo pagamento (parcial ou total).
     * valor_pago	decimal(10,2)			Não	Nenhum			Muda Muda	Eliminar Eliminar	
	3	forma_pagamento	varchar(45)	utf8mb4_unicode_ci		Não	Nenhum			Muda Muda	Eliminar Eliminar	
	4	status_pagamento	enum('PENDENTE', 'PAGO', 'CANCELADO', 'ESTORNADO')	utf8mb4_unicode_ci		Não	PENDENTE			Muda Muda	Eliminar Eliminar	
	5	data_pagamento	timestamp			Sim	NULL			Muda Muda	Eliminar Eliminar	
	6	observacao	varchar(255)	utf8mb4_unicode_ci		Sim	NULL			Muda Muda	Eliminar Eliminar	
	7	orcamento_paciente_id Índice	bigint		UNSIGNED	Não	Nenhum			Muda Muda	Eliminar Eliminar	
	8	user_id Índice
     */

    private function atualizarSaldoOrcamento(OrcamentoPaciente $orcamento)
    {
        $valorBase = (float) ($orcamento->valor_liquido ?? $orcamento->total_orcamento ?? 0);

        // ✅ Soma PAGO + PENDENTE, ignora cancelados/estornados
        $totalPago = (float) Pagamento::where('orcamento_paciente_id', $orcamento->id)
            ->whereIn('status_pagamento', ['PAGO', 'PENDENTE'])
            ->sum('valor_pago');

        $saldoRestante = max($valorBase - $totalPago, 0);

        // ✅ Salva com precisão decimal
        $orcamento->updateQuietly([
            'saldo_restante' => number_format($saldoRestante, 2, '.', '')
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'orcamento_paciente_id' => 'required|exists:orcamento_pacientes,id',
            'valor_pago' => [
                'required',
                'numeric',
                'min:0.01',
                function ($atributo, $valor, $falha) use ($request) {
                    $orcamento = OrcamentoPaciente::findOrFail($request->orcamento_paciente_id);
                    $valorBase = $orcamento->valor_liquido ?? $orcamento->total_orcamento ?? 0;

                    // ✅ ALINHADO: mesma regra do método de atualização
                    $totalPago = Pagamento::where('orcamento_paciente_id', $orcamento->id)
                        ->whereIn('status_pagamento', ['PAGO', 'PENDENTE'])
                        ->sum('valor_pago');

                    $saldoRestante = $valorBase - $totalPago;

                    if ($valor > $saldoRestante) {
                        $falha("O valor pago não pode ultrapassar o saldo restante de R$ " . number_format($saldoRestante, 2, ',', '.'));
                    }
                },
            ],
            'forma_pagamento' => 'required|string|max:45',
            'status_pagamento' => 'required|in:PENDENTE,PAGO,CANCELADO,ESTORNADO',
            'observacao' => 'nullable|string|max:255',
        ]);

        $dados['user_id'] = auth()->id();

        DB::beginTransaction();
        try {
            $orcamento = OrcamentoPaciente::findOrFail($dados['orcamento_paciente_id']);
            $pagamento = Pagamento::create($dados);

            // ✅ Atualiza saldo com a regra correta
            $this->atualizarSaldoOrcamento($orcamento);

            DB::commit();
            return redirect()->route('pagamento.index')
                ->with('sucesso', 'Pagamento salvo e saldo atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd('ERRO FINAL:', $e->getMessage());
            return back()->withErrors('Erro: ' . $e->getMessage());
        }
    }
    /**
     * Exibe detalhes de um pagamento.
     */
    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['paciente', 'orcamento', 'formaPagamento']);
        return response()->json($pagamento);
    }

    /**
     * Retorna dados para edição.
     */
    public function edit(Pagamento $pagamento)
    {
        // ✅ Carrega todos os relacionamentos necessários
        $pagamento->load(['orcamentoPaciente.paciente', 'orcamentoPaciente.pagamentos']);

        // ✅ Cria a variável $orcamento que a view espera
        $orcamento = $pagamento->orcamentoPaciente;

        // ✅ Usa a mesma view para cadastro e edição
        return view('dentista.pagamento.formulario', compact('pagamento', 'orcamento'));
    }

    /**
     * Atualiza dados de um pagamento existente.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        $dados = $request->validate([
            'orcamento_paciente_id' => 'required|exists:orcamento_pacientes,id',
            'valor_pago' => [
                'required',
                'numeric',
                'min:0.01',
                function ($atributo, $valor, $falha) use ($request, $pagamento) {
                    $orcamento = OrcamentoPaciente::findOrFail($request->orcamento_paciente_id);
                    $valorBase = $orcamento->valor_liquido ?? $orcamento->total_orcamento ?? 0;

                    // Soma pagamentos do orçamento, EXCLUINDO o pagamento atual (para permitir alterar valor)
                    $totalPago = Pagamento::where('orcamento_paciente_id', $orcamento->id)
                        ->where('id', '!=', $pagamento->id) // ✅ Ignora o próprio registro na soma
                        ->whereIn('status_pagamento', ['PAGO', 'PENDENTE'])
                        ->sum('valor_pago');

                    $saldoRestante = $valorBase - $totalPago;

                    if ($valor > $saldoRestante) {
                        $falha("O valor pago não pode ultrapassar o saldo restante de R$ " . number_format($saldoRestante, 2, ',', '.'));
                    }
                },
            ],
            'forma_pagamento' => 'required|string|max:45',
            'status_pagamento' => 'required|in:PENDENTE,PAGO,CANCELADO,ESTORNADO',
            'observacao' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $pagamento->update($dados);

            // Atualiza saldo do orçamento após alteração
            $this->atualizarSaldoOrcamento($pagamento->orcamentoPaciente);

            DB::commit();
            return redirect()->route('pagamento.index')
                ->with('sucesso', 'Pagamento atualizado e saldo do orçamento ajustado!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd('ERRO NA EDIÇÃO:', $e->getMessage());
            return back()->withErrors('Erro ao atualizar: ' . $e->getMessage());
        }
    }
    /**
     * Exclui ou estorna pagamento (regra fiscal).
     */
    public function destroy(Pagamento $pagamento)
    {
        DB::beginTransaction();
        try {
            $orcamento = $pagamento->orcamentoPaciente;
            $pagamento->delete();

            // ✅ Atualiza saldo após exclusão
            $this->atualizarSaldoOrcamento($orcamento);

            DB::commit();
            return redirect()->route('pagamento.index')->with('sucesso', 'Pagamento excluído e saldo atualizado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Erro ao excluir: ' . $e->getMessage());
        }
    }
}
