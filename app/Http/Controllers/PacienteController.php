<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\EnderecoPaciente;
use App\Models\ContatoPaciente;
use App\Models\HabitoSaudavel;
use App\Models\HistoricoBucal;
use App\Models\HistoricoMedico;
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
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%");
            });
        }

        // Retorna a listagem paginada de 15 em 15 registros
        $pacientes = $query->orderBy('nome', 'asc')->paginate(15);

        // return response()->json($pacientes);
        return view('dentista.paciente.index', compact('pacientes'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        return view('dentista.paciente.create');
    }

    /**
     * Cadastra um novo paciente e inicializa seu endereço base.
     */
    public function store(Request $request)
    {
        // 1. Validação com os nomes exatos que vêm do formulário
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pacientes,cpf',
            'rg' => 'required|string|max:14',
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'nullable|string|in:Masculino,Feminino,Outro',
            'telefone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',

            // 3. LOCALIZAÇÃO RESIDENCIAL
            'cep' => 'required|string|max:9',
            'pais' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cidade' => 'required|string|max:100',
            'bairro' => 'required|string|max:100',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',

            // 4. HISTÓRICO MÉDICO
            'alergias' => 'nullable|string|max:255',
            'medicacaoEmUso' => 'nullable|string|max:255',
            'condicaoCronica' => 'nullable|string|max:255',
            'historicoCirurgico' => 'nullable|string|max:1000',
            'observacaoMedicaGeral' => 'nullable|string|max:1000',

            // 5. HISTÓRICO BUCAL
            'ultima_consulta' => 'nullable|date|before_or_equal:today',
            'motivo_consulta_atual' => 'nullable|string|max:255',
            'sangramento_gengival' => 'nullable|string|in:Sim,Não',
            'sensibilidade_dental' => 'nullable|string|in:Sim,Não',
            'uso_proteses_aparelho' => 'nullable|string|in:Sim,Não',
            'tipo_aparelho_ou_protese' => 'nullable|string|max:255',
            'tratamento_bucal_anterior' => 'nullable|string|max:1000',
            'observacoes_bucal_adicionais' => 'nullable|string|max:1000',

            // 6. HÁBITOS SAUDÁVEIS
            'frequencia_escovacao' => 'nullable|string|in:1 vez ao dia,2 vezes ao dia,3 ou mais vezes ao dia',
            'usa_fio_dental' => 'nullable|string|in:Sim,Não,Às vezes',
            'habito_fumo' => 'nullable|string|in:Não fuma,Ex-fumante,Fuma ocasionalmente,Fumante regular',
            'atividade_fisica' => 'nullable|string|in:Não pratica,1-2 vezes por semana,3-4 vezes por semana,Diariamente',
            'qualidade_sono' => 'nullable|string|in:Boa,Razoável,Ruim',
            'observacoes_estilo_vida' => 'nullable|string|max:1000', // Corrigido a regra que estava errada aqui
        ]);

        // 2. Uso de Transaction usando a variável $validated
        $paciente = DB::transaction(function () use ($validated) {

            // Salva os dados principais do paciente
            $paciente = Paciente::create([
                'nome' => $validated['nome'],
                'sobrenome' => $validated['sobrenome'],
                'cpf' => $validated['cpf'],
                'rg' => $validated['rg'],
                'data_nascimento' => $validated['data_nascimento'],
                'sexo' => $validated['sexo'] ?? null,
                'telefone' => $validated['telefone'],
                'whatsapp' => $validated['whatsapp'],
                'email' => $validated['email'],
            ]);

            //contato paciente 
            ContatoPaciente::create([
                'paciente_id' => $paciente->id,
                'telefone' => $validated['telefone'],
                'whatsapp' => $validated['whatsapp'],
                'email' => $validated['email'],
            ]);

            // Cria o registro de endereço vinculado ao paciente
            EnderecoPaciente::create([
                'paciente_id' => $paciente->id,
                'cep' => $validated['cep'],
                'País' => $validated['pais'], // Removido o 'P' maiúsculo e acento para alinhar com o padrão
                'estado' => $validated['estado'],
                'cidade' => $validated['cidade'],
                'bairro' => $validated['bairro'],
                'rua' => $validated['rua'],
                'numero' => $validated['numero'],
            ]);

            // Cria o Histórico Médico associando os campos validados corretos
            HistoricoMedico::create([
                'paciente_id' => $paciente->id,
                'alergias' => $validated['alergias'] ?? null,
                'medicacoes' => $validated['medicacaoEmUso'] ?? null,
                'condicoes_cronicas' => $validated['condicaoCronica'] ?? null,
                'historico_cirurgico' => $validated['historicoCirurgico'] ?? null,
                'observacoes_medicas' => $validated['observacaoMedicaGeral'] ?? null,
            ]);

            // Cria o Histórico Bucal associando os campos validados corretos
            HistoricoBucal::create([
                'paciente_id' => $paciente->id,
                'data_ultima_visita' => $validated['ultima_consulta'] ?? null,
                'motivo_visita' => $validated['motivo_consulta_atual'] ?? null,
                'sangramento_gengival' => $validated['sangramento_gengival'] ?? null,
                'sensibilidade_dental' => $validated['sensibilidade_dental'] ?? null,
                'uso_protese' => $validated['uso_proteses_aparelho'] ?? null,
                'tipo_protese' => $validated['tipo_aparelho_ou_protese'] ?? null,
                'tratamentos_anteriores' => $validated['tratamento_bucal_anterior'] ?? null,
                'observacoes_bucais' => $validated['observacoes_bucal_adicionais'] ?? null,
            ]);

            // Cria o registro de Hábitos Saudáveis
            HabitoSaudavel::create([
                'paciente_id' => $paciente->id,
                'frequencia_escovacao' => $validated['frequencia_escovacao'] ?? null,
                'usa_fio_dental' => $validated['usa_fio_dental'] ?? null,
                'habito_fumo' => $validated['habito_fumo'] ?? null,
                'consumo_alcool' => $validated['observacoes_estilo_vida'] ?? null, // Mapeado conforme o que restou na validação anterior
                'pratica_atividade_fisica' => $validated['atividade_fisica'] ?? null,
                'qualidade_sono' => $validated['qualidade_sono'] ?? null,
            ]);

            return $paciente;
        });

        return view('dentista.paciente.index', compact('paciente'));
        // Retorna resposta JSON de sucesso
        return response()->json([
            'message' => 'Paciente cadastrado com sucesso no sistema!',
            'data' => $paciente->load('enderecoPaciente') // Certifique-se de carregar os outros relacionamentos aqui se precisar no retorno, ex: load(['enderecoPaciente', 'historicoBucal'])
        ], 201);
    }

    /**
     * Exibe a ficha clínica e cadastral completa do paciente (Prontuário/Perfil).
     */
    public function show(Paciente $paciente)
    {
        // Carrega todas as dependências mapeadas no Modelo (Ficha completa de uma só vez)
        $paciente->load([
            'enderecoPaciente',
            'contatosPaciente',
            'historicoMedico',
            'habitoSaudavel',
            'historicoBucal',
        ]);

        return response()->json($paciente);
    }

    /**
     * Retorna os dados do paciente para edição.
     */
    public function edit(Paciente $paciente)
    {
        $paciente->load(['enderecoPaciente', 'contatosPaciente', 'historicoBucal', 'historicoMedico', 'habitoSaudavel']);
        return view('dentista.paciente.edit', compact('paciente'));
    }

    /**
     * Atualiza os dados pessoais e o endereço residencial do paciente.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pacientes,cpf,' . $paciente->id, // Ignora o próprio ID
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'required|string|in:Masculino,Feminino,Outro',
            'status' => 'required|string|in:Ativo,Inativo',

            'endereco.cep' => 'required|string|max:9',
            'endereco.pais' => 'required|string|max:100',
            'endereco.estado' => 'required|string|size:2',
            'endereco.cidade' => 'required|string|max:100',
            'endereco.bairro' => 'required|string|max:100',
            'endereco.rua' => 'required|string|max:255',
            'endereco.numero' => 'required|string|max:20',


        ]);

        DB::transaction(function () use ($request, $paciente) {

            // Atualiza os dados pessoais
            $paciente->update([
                'nome' => $request->nome,
                'cpf' => $request->cpf,
                'data_nascimento' => $request->data_nascimento,
                'sexo' => $request->sexo
            ]);

            // Atualiza ou cria o endereço caso não existisse previamente
            $paciente->endereco()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                $request->endereco
            );
        });

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