<?php

namespace App\Http\Controllers;

use App\Models\Procedimento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcedimentoController extends Controller
{
    /**
     * Exibe a listagem do catálogo de procedimentos (com busca e paginação).
     */
    public function index(Request $request)
    {
        $query = Procedimento::where('user_id', auth()->id());

        if ($request->has('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        // ✅ Busca APENAS por nome (campo que realmente existe)
        if ($request->has('busca')) {
            $busca = $request->busca;
            $query->where('nome', 'like', "%{$busca}%");
        }

        $procedimentos = $query->orderBy('nome', 'asc')->paginate(20);

        return view('dentista.procedimento.index', compact('procedimentos'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        return view('dentista.procedimento.formulario');
    }

    // Atualiza o registro no banco
    public function update(Request $request, Procedimento $procedimento)
    {
        $dadosValidados = $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'ativo' => 'required|in:0,1',
        ]);

        $procedimento->update($dadosValidados);

        return redirect()->route('procedimento.index')->with('success', 'Procedimento atualizado com sucesso!');
    }

    /**
     * Salva um novo procedimento no catálogo global.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados (o ID do usuário não fica no formulário por segurança)
        $dadosValidados = $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'ativo' => 'required|in:0,1',
        ]);

        // 2. Injeta automaticamente o ID do usuário autenticado no array
        $dadosValidados['user_id'] = auth()->id(); // <-- Ajuste para 'dentista_id' se sua coluna no banco usar esse nome

        // 3. Salva o registro já com o vínculo do usuário logado
        $procedimento = Procedimento::create($dadosValidados);

        return redirect()
            ->route('procedimento.index')
            ->with('success', 'Procedimento criado com sucesso!');
    }

    /**
     * Exibe os detalhes de um procedimento específico.
     */
    public function show(Procedimento $procedimento)
    {
        return response()->json($procedimento);
    }

    /**
     * Retorna os dados do procedimento para edição.
     */
    public function edit(Procedimento $procedimento)
    {
        return view('dentista.procedimento.formulario', compact('procedimento'));
    }

    /**
     * Atualiza os dados de um procedimento no catálogo.
     */


    /**
     * Remove ou desativa um procedimento do catálogo.
     */
    public function destroy(Procedimento $procedimento)
    {
        // Verifica se o procedimento já foi utilizado em algum orçamento
        $jaFoiUtilizado = $procedimento->itensOrcamento()->exists();

        if ($jaFoiUtilizado) {

            // Apenas inativa o procedimento para preservar o histórico
            $procedimento->update([
                'ativo' => false
            ]);

            return redirect()
                ->route('procedimento.index')
                ->with('warning', 'O procedimento não pode ser excluído porque já foi utilizado em um ou mais orçamentos. Ele foi apenas inativado e não ficará mais disponível para novos atendimentos.');
        }

        // Caso nunca tenha sido utilizado, exclui definitivamente
        $procedimento->delete();

        return redirect()
            ->route('procedimento.index')
            ->with('success', 'Procedimento excluído com sucesso.');
    }
}
