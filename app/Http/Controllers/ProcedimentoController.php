<?php

namespace App\Http\Controllers;

use App\Models\Procedimento;
use Illuminate\Http\Request;

class ProcedimentoController extends Controller
{
    /**
     * Exibe a listagem do catálogo de procedimentos (com busca e paginação).
     */
    public function index(Request $request)
    {
        $query = Procedimento::query();

        // Filtro por status (ex: buscar apenas procedimentos 'Ativos')
        if ($request->has('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        // Filtro por termo de busca (Nome ou Código Clínico/TUSS)
        if ($request->has('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('codigo_clinico', 'like', "%{$busca}%");
            });
        }

        // Traz os procedimentos ordenados em ordem alfabética e paginados
        $procedimentos = $query->orderBy('nome', 'asc')->paginate(20);

        return view('dentista.procedimento.index', compact('procedimentos'));
       
        // return response()->json($procedimentos);
        // Se usar Blade: return view('configuracoes.procedimentos.index', compact('procedimentos'));
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
         $dadosValidados = $request->validate([
            'nome' => 'required|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:1',
            'ativo' => 'required|in:0,1',
        ]);

        $procedimento = Procedimento::create($dadosValidados);

         return redirect()->route('procedimento.index')->with('success', 'Procedimento criado com sucesso!');

        // return response()->json([
        //     'message' => 'Procedimento cadastrado com sucesso no catálogo!',
        //     'data' => $procedimento
        // ], 201);
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
        // Regra de Negócio: Se o procedimento já foi inserido em algum item de orçamento de paciente,
        // ele NÃO pode ser deletado fisicamente do banco de dados para evitar inconsistência financeira e quebra de relatórios históricos.
        $jaFoiUtilizado = $procedimento->itensOrcamento()->exists();

        if ($jaFoiUtilizado) {
            // Em vez de apagar, altera o status para 'false' (Inativo)
            // Assim ele some de novas listagens de venda, mas mantém o passado intacto
            $procedimento->update(['ativo' => false]);

            return response()->json([
                'message' => 'Este procedimento já possui vínculos em orçamentos e não pode ser deletado. Seu status foi alterado para Inativo no catálogo.'
            ], 200);
        }

        // Se nunca tiver sido utilizado em nenhum orçamento clínico, realiza a exclusão física
        $procedimento->delete();

        return response()->json(['message' => 'Procedimento excluído com sucesso do sistema.']);
    }
}