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

        return response()->json($procedimentos);
        // Se usar Blade: return view('configuracoes.procedimentos.index', compact('procedimentos'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        // Retorna a view para cadastrar um novo procedimento
    }

    /**
     * Salva um novo procedimento no catálogo global.
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'nome'            => 'required|string|max:255|unique:procedimentos,nome', // Evita duplicar nomes
            'codigo_clinico'   => 'nullable|string|max:50',                          // Código interno ou TUSS
            'valor_base'       => 'required|numeric|min:0',                          // Valor padrão sugerido
            'tempo_estimado'   => 'required|integer|min:10',                         // Duração em minutos (mínimo de 10 min)
            'ativo'            => 'required|boolean'
        ]);

        $procedimento = Procedimento::create($dadosValidados);

        return response()->json([
            'message' => 'Procedimento cadastrado com sucesso no catálogo!',
            'data' => $procedimento
        ], 201);
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
        return response()->json($procedimento);
    }

    /**
     * Atualiza os dados de um procedimento no catálogo.
     */
    public function update(Request $request, Procedimento $procedimento)
    {
        $dadosValidados = $request->validate([
            'nome'            => 'required|string|max:255|unique:procedimentos,nome,' . $procedimento->id, // Ignora o próprio ID
            'codigo_clinico'   => 'nullable|string|max:50',
            'valor_base'       => 'required|numeric|min:0',
            'tempo_estimado'   => 'required|integer|min:10',
            'ativo'            => 'required|boolean'
        ]);

        $procedimento->update($dadosValidados);

        return response()->json([
            'message' => 'Procedimento atualizado com sucesso!',
            'data' => $procedimento
        ]);
    }

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