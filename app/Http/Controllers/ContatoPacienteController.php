<?php

namespace App\Http\Controllers;

use App\Models\ContatoPaciente;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ContatoPacienteController extends Controller
{
    /**
     * Exibe a listagem de contatos (Filtrada por um paciente específico).
     */
    public function index(Request $request)
    {
        $query = ContatoPaciente::with('paciente');

        // Se o ID do paciente for fornecido na requisição, filtra apenas os contatos dele
        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        $contatos = $query->get();

        return response()->json($contatos);
    }

    /**
     * Auxilia o carregamento de dados para criação (caso use views Blade).
     */
    public function create()
    {
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        return response()->json(compact('pacientes'));
    }

    /**
     * Salva um novo canal de contato para o paciente.
     */
    public function store(Request $request)
    {
        // Validação dos dados informados
        $dadosValidados = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo'        => 'required|string|in:Celular,Fixo,E-mail,Responsável,Recado',
            'valor'       => 'required|string|max:150', // Número de telefone ou o endereço de e-mail
            'principal'   => 'required|boolean',        // Define se é o meio padrão de contato
            'observacoes' => 'nullable|string|max:255'
        ]);

        // Regra de Negócio: Se este novo contato for definido como o Principal,
        // o Laravel desmarca automaticamente todos os outros contatos antigos deste paciente.
        if ($request->principal) {
            ContatoPaciente::where('paciente_id', $request->paciente_id)
                ->update(['principal' => false]);
        }

        $contato = ContatoPaciente::create($dadosValidados);

        return response()->json([
            'message' => 'Contato cadastrado com sucesso!',
            'data' => $contato
        ], 201);
    }

    /**
     * Exibe um contato específico.
     */
    public function show(ContatoPaciente $contatoPaciente)
    {
        $contatoPaciente->load('paciente');
        return response()->json($contatoPaciente);
    }

    /**
     * Retorna os dados do contato para edição.
     */
    public function edit(ContatoPaciente $contatoPaciente)
    {
        return response()->json($contatoPaciente);
    }

    /**
     * Atualiza os dados de um contato existente do paciente.
     */
    public function update(Request $request, ContatoPaciente $contatoPaciente)
    {
        $dadosValidados = $request->validate([
            'tipo'        => 'required|string|in:Celular,Fixo,E-mail,Responsável,Recado',
            'valor'       => 'required|string|max:150',
            'principal'   => 'required|boolean',
            'observacoes' => 'nullable|string|max:255'
        ]);

        // Regra de Negócio: Se foi atualizado para principal, desmarca os anteriores
        if ($request->principal) {
            ContatoPaciente::where('paciente_id', $contatoPaciente->paciente_id)
                ->where('id', '!=', $contatoPaciente->id)
                ->update(['principal' => false]);
        }

        $contatoPaciente->update($dadosValidados);

        return response()->json([
            'message' => 'Contato atualizado com sucesso!',
            'data' => $contatoPaciente
        ]);
    }

    /**
     * Remove o contato selecionado.
     */
    public function destroy(ContatoPaciente $contatoPaciente)
    {
        $contatoPaciente->delete();

        return response()->json(['message' => 'Contato removido com sucesso do prontuário do paciente.']);
    }
}