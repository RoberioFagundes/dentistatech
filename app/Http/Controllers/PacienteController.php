<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\EnderecoPaciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    /**
     * Exibe a listagem de pacientes com busca global e paginação.
     */
    public function index(Request $request)
    {
        $query = Paciente::query();

        // Filtro por Status (Ex: 'Ativo', 'Inativo')
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Busca global por Nome ou CPF
        if ($request->has('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('cpf', 'like', "%{$busca}%");
            });
        }

        // Retorna a listagem paginada de 15 em 15 registros
        $pacientes = $query->orderBy('nome', 'asc')->paginate(15);

        return response()->json($pacientes);
        // Se usar Blade: return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        // Retorna a view para cadastrar um novo paciente
    }

    /**
     * Cadastra um novo paciente e inicializa seu endereço base.
     */
    public function store(Request $request)
    {
        // 1. Validação rigorosa dos dados pessoais e do endereço obrigatório
        $request->validate([
            'nome'            => 'required|string|max:255',
            'cpf'             => 'required|string|max:14|unique:pacientes,cpf', // CPF único no sistema
            'data_nascimento' => 'required|date|before:today',
            'genero'          => 'required|string|in:Masculino,Feminino,Outro',
            'status'          => 'required|string|in:Ativo,Inativo',
            
            // Validação dos dados aninhados de endereço
            'endereco.cep'    => 'required|string|max:9',
            'endereco.rua'    => 'required|string|max:255',
            'endereco.numero' => 'required|string|max:20',
            'endereco.bairro' => 'required|string|max:100',
            'endereco.cidade' => 'required|string|max:100',
            'endereco.uf'     => 'required|string|size:2',
        ]);

        // 2. Uso de Transaction para garantir que o paciente e o endereço sejam gravados juntos
        $paciente = DB::transaction(function () use ($request) {
            
            // Salva os dados do paciente
            $paciente = Paciente::create([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'genero'          => $request->genero,
                'status'          => $request->status,
            ]);

            // Cria o registro de endereço vinculado a este novo paciente
            EnderecoPaciente::create(array_merge($request->endereco, [
                'paciente_id' => $paciente->id
            ]));

            return $paciente;
        });

        return response()->json([
            'message' => 'Paciente cadastrado com sucesso no sistema!',
            'data' => $paciente->load('endereco')
        ], 201);
    }

    /**
     * Exibe a ficha clínica e cadastral completa do paciente (Prontuário/Perfil).
     */
    public function show(Paciente $paciente)
    {
        // Carrega todas as dependências mapeadas no Modelo (Ficha completa de uma só vez)
        $paciente->load([
            'endereco', 
            'contatos', 
            'historicoSaude', 
            'habitoBucal', 
            'hospitalizacoes'
        ]);

        return response()->json($paciente);
    }

    /**
     * Retorna os dados do paciente para edição.
     */
    public function edit(Paciente $paciente)
    {
        $paciente->load('endereco');
        return response()->json($paciente);
    }

    /**
     * Atualiza os dados pessoais e o endereço residencial do paciente.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nome'            => 'required|string|max:255',
            'cpf'             => 'required|string|max:14|unique:pacientes,cpf,' . $paciente->id, // Ignora o próprio ID
            'data_nascimento' => 'required|date|before:today',
            'genero'          => 'required|string|in:Masculino,Feminino,Outro',
            'status'          => 'required|string|in:Ativo,Inativo',
            
            'endereco.cep'    => 'required|string|max:9',
            'endereco.rua'    => 'required|string|max:255',
            'endereco.numero' => 'required|string|max:20',
            'endereco.bairro' => 'required|string|max:100',
            'endereco.cidade' => 'required|string|max:100',
            'endereco.uf'     => 'required|string|size:2',
        ]);

        DB::transaction(function () use ($request, $paciente) {
            
            // Atualiza os dados pessoais
            $paciente->update([
                'nome'            => $request->nome,
                'cpf'             => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'genero'          => $request->genero,
                'status'          => $request->status,
            ]);

            // Atualiza ou cria o endereço caso não existisse previamente
            $paciente->endereco()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                $request->endereco
            );
        ]);

        return response()->json([
            'message' => 'Ficha cadastral do paciente atualizada com sucesso!'
        ]);
    }

    /**
     * Remove ou desativa o paciente, respeitando o histórico legal odontológico.
     */
    public function destroy(Paciente $paciente)
    {
        // Regra de Negócio Crítica: Clínicas de saúde não podem excluir fisicamente pacientes que já possuem 
        // históricos de consultas (agendamentos), evoluções de prontuário ou orçamentos aprovados devido a regras do CRO e auditorias legais.
        $possuiHistoricoClinico = $paciente->agendamentos()->exists() || 
                                  $paciente->orcamentos()->exists();

        if ($possuiHistoricoClinico) {
            // Em vez de apagar do banco, aplica a exclusão lógica/desativação mudando o status para 'Inativo'
            $paciente->update(['status' => 'Inativo']);

            return response()->json([
                'message' => 'O paciente possui históricos clínicos registrados e não pode ser excluído permanentemente. Seu status foi alterado para Inativo.'
            ], 200);
        }

        // Se for um cadastro novo sem nenhum vínculo ativo, realiza a deleção completa das tabelas filhas e do pai
        DB::transaction(function () use ($paciente) {
            $paciente->endereco()->delete();
            $paciente->contatos()->delete();
            $paciente->delete();
        });

        return response()->json([
            'message' => 'Registro do paciente excluído definitivamente do sistema.'
        ]);
    }
}