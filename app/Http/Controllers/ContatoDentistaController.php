<?php

namespace App\Http\Controllers;

use App\Models\ContatoDentista;
use App\Models\Dentista;
use Illuminate\Http\Request;

class ContatoDentistaController extends Controller
{
    /**
     * Exibe a listagem de contatos (Geralmente filtrada por um dentista específico).
     */
    public function index(Request $request)
    {
        $query = ContatoDentista::with('dentista');

        // Se passar o ID do dentista na URL, filtra os contatos apenas dele
        if ($request->has('dentista_id')) {
            $query->where('dentista_id', $request->dentista_id);
        }

        $contatos = $query->get();

        return response()->json($contatos);
    }

    /**
     * Exibe o formulário/dados auxiliares para criar um contato.
     */
    public function create(Request $request)
    {
        // Se houver uma tela com select, traz os dentistas ativos para vincular o contato
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();
        return response()->json(compact('dentistas'));
    }

    /**
     * Salva um novo contato para o dentista.
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'dentista_id' => 'required|exists:dentistas,id',
            'tipo'        => 'required|string|in:Celular,Fixo,E-mail,Recado', // Define os tipos aceitos
            'valor'       => 'required|string|max:150', // O número ou o e-mail em si
            'principal'   => 'required|boolean',        // Identifica se é o contato padrão
            'observacoes' => 'nullable|string|max:255'
        ]);

        // Regra de Negócio: Se este novo contato for marcado como principal, 
        // desmarca os outros contatos antigos deste mesmo dentista
        if ($request->principal) {
            ContatoDentista::where('dentista_id', $request->dentista_id)
                ->update(['principal' => false]);
        }

        $contato = ContatoDentista::create($dadosValidados);

        return response()->json([
            'message' => 'Contato cadastrado com sucesso!',
            'data' => $contato
        ], 201);
    }

    /**
     * Exibe um contato específico.
     */
    public function show(ContatoDentista $contatoDentista)
    {
        $contatoDentista->load('dentista');
        return response()->json($contatoDentista);
    }

    /**
     * Exibe os dados do contato para edição.
     */
    public function edit(ContatoDentista $contatoDentista)
    {
        return response()->json($contatoDentista);
    }

    /**
     * Atualiza os dados de um contato específico.
     */
    public function update(Request $request, ContatoDentista $contatoDentista)
    {
        $dadosValidados = $request->validate([
            'tipo'        => 'required|string|in:Celular,Fixo,E-mail,Recado',
            'valor'       => 'required|string|max:150',
            'principal'   => 'required|boolean',
            'observacoes' => 'nullable|string|max:255'
        ]);

        // Mesma Regra de Negócio: Se foi atualizado para principal, desmarca os demais
        if ($request->principal) {
            ContatoDentista::where('dentista_id', $contatoDentista->dentista_id)
                ->where('id', '!=', $contatoDentista->id)
                ->update(['principal' => false]);
        }

        $contatoDentista->update($dadosValidados);

        return response()->json([
            'message' => 'Contato atualizado com sucesso!',
            'data' => $contatoDentista
        ]);
    }

    /**
     * Remove o canal de contato do sistema.
     */
    public function destroy(ContatoDentista $contatoDentista)
    {
        // Regra de segurança: Se for o único contato principal, pode ser interessante avisar ou bloquear
        $contatoDentista->delete();

        return response()->json(['message' => 'Contato removido com sucesso.']);
    }
}