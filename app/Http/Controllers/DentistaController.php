<?php

namespace App\Http\Controllers;

use App\Models\Dentista;
use Illuminate\Http\Request;

class DentistaController extends Controller
{
    /**
     * Exibe a listagem de dentistas (com filtros opcionais).
     */
    public function index(Request $request)
    {
        $query = Dentista::query();

        // Permite filtrar por status (ex: buscar apenas os 'Ativos')
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Busca por nome ou especialidade
        if ($request->has('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('especialidade', 'like', "%{$busca}%");
            });
        }

        // Traz os dentistas paginados com a contagem de contatos cadastrados
        $dentistas = $query->withCount('contatos')
            ->orderBy('nome', 'asc')
            ->paginate(10);

        return response()->json($dentistas);
        // Se usar Blade: return view('dentistas.index', compact('dentistas'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        // Retorna a view ou dados estruturais para o formulário
    }

    /**
     * Salva um novo dentista no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação rigorosa dos dados profissionais e cadastrais
        $dadosValidados = $request->validate([
            'nome'          => 'required|string|max:255',
            'cro'           => 'required|string|max:20|unique:dentistas,cro', // CRO deve ser único
            'cro_uf'        => 'required|string|size:2',                      // Sigla do estado (Ex: SP, RJ)
            'especialidade' => 'required|string|max:150',                     // Ex: Ortodontia, Endodontia
            'status'        => 'required|string|in:Ativo,Inativo,Afastado',
        ]);

        $dentista = Dentista::create($dadosValidados);

        return response()->json([
            'message' => 'Dentista cadastrado com sucesso!',
            'data' => $dentista
        ], 201);
    }

    /**
     * Exibe o perfil detalhado de um dentista específico (incluindo contatos e endereço).
     */
    public function show(Dentista $dentista)
    {
        // Carrega os relacionamentos através do Route Model Binding do Laravel
        $dentista->load(['contatos', 'endereco']);

        return response()->json($dentista);
    }

    /**
     * Exibe o formulário de edição pré-carregando os dados do profissional.
     */
    public function edit(Dentista $dentista)
    {
        return response()->json($dentista);
    }

    /**
     * Atualiza os dados cadastrais do dentista.
     */
    public function update(Request $request, Dentista $dentista)
    {
        $dadosValidados = $request->validate([
            'nome'          => 'required|string|max:255',
            'cro'           => 'required|string|max:20|unique:dentistas,cro,' . $dentista->id, // Ignora o próprio ID na validação de unique
            'cro_uf'        => 'required|string|size:2',
            'especialidade' => 'required|string|max:150',
            'status'        => 'required|string|in:Ativo,Inativo,Afastado',
        ]);

        $dentista->update($dadosValidados);

        return response()->json([
            'message' => 'Cadastro do dentista atualizado com sucesso!',
            'data' => $dentista
        ]);
    }

    /**
     * Remove ou desativa o dentista do sistema.
     */
    public function destroy(Dentista $dentista)
    {
        // Regra de Negócio: Se o dentista já tiver históricos clínicos (agendamentos ou prontuários),
        // ele NÃO pode ser deletado fisicamente do banco de dados para não quebrar a integridade referencial.
        $possuiVinculos = $dentista->agendamentos()->exists() || $dentista->prontuarios()->exists();

        if ($possuiVinculos) {
            // Em vez de deletar, altera o status para 'Inativo' salvaguardando os dados históricos da clínica
            $dentista->update(['status' => 'Inativo']);

            return response()->json([
                'message' => 'O dentista possui históricos no sistema e não pode ser excluído. Seu status foi alterado para Inativo.'
            ], 200);
        }

        // Se não houver histórico atrelado, a exclusão física é liberada
        $dentista->delete();

        return response()->json([
            'message' => 'Registro do dentista removido com sucesso do sistema.'
        ]);
    }
}