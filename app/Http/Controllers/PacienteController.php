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
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    /**
     * Exibe a listagem de pacientes com busca global e paginação.
     */
    public function index(Request $request)
    {
        /// Adicione o 'with' para carregar os contatos junto com os pacientes
        $query = Paciente::where('user_id', auth()->id())
            ->with('contatosPaciente', 'habitoSaudavel', 'historicoBucal', 'historicoMedico'); // <--- A MÁGICA ESTÁ AQUI

        // ... restante do seu código (filtros de status e busca)

        // A busca também deve considerar o relacionamento se necessário
        if ($request->filled('busca')) {
            $busca = trim($request->busca);

            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('sobrenome', 'like', "%{$busca}%")
                    ->orWhereRaw("CONCAT(nome, ' ', sobrenome) LIKE ?", ["%{$busca}%"]);
            });
        }

        $pacientes = $query->orderBy('nome', 'asc')->paginate(15);

        return view('dentista.paciente.index', compact('pacientes'));
    }
    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        $paciente = new Paciente(); // Cria uma instância vazia para o formulário
        return view('dentista.paciente.formulario', compact('paciente'));
    }

    /**
     * Cadastra um novo paciente e inicializa seu endereço base.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();
        // 1. Validação com os nomes exatos que vêm do formulário
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cpf' => [
        'required',
        'string',
        Rule::unique('pacientes', 'cpf')->where('user_id', $userId),
    ],
            'rg' => 'required|string|max:14',
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'nullable|string|in:Masculino,Feminino,Outro',
            'telefone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',

            // LOCALIZAÇÃO RESIDENCIAL
            'cep' => 'required|string|max:9',
            'pais' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cidade' => 'required|string|max:100',
            'bairro' => 'required|string|max:100',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',

            // HISTÓRICO MÉDICO
            'alergias' => 'nullable|string|max:255',
            'medicacaoEmUso' => 'nullable|string|max:255',
            'condicaoCronica' => 'nullable|string|max:255',
            'historicoCirurgico' => 'nullable|string|max:1000',
            'observacaoMedicaGeral' => 'nullable|string|max:1000',


            // HISTÓRICO BUCAL
            'ultima_consulta' => 'nullable|date|before_or_equal:today',
            'motivo_consulta_atual' => 'nullable|string|max:255',
            'sangramento_gengival' => 'nullable|string|in:Sim,Não',
            'sensibilidade_dental' => 'nullable|string|in:Sim,Não',
            'uso_proteses_aparelho' => 'nullable|string|in:Sim,Não',
            'tipo_aparelho_ou_protese' => 'nullable|string|max:255',
            'tratamento_bucal_anterior' => 'nullable|string|max:1000',
            'observacoes_bucal_adicionais' => 'nullable|string|max:1000',

            // HÁBITOS SAUDÁVEIS
            'frequencia_escovacao' => 'nullable|string|in:1 vez ao dia,2 vezes ao dia,3 ou mais vezes ao dia',
            'uso_fio_dental' => 'nullable|string|in:Sim,Não,Às vezes',
            'habito_fumo' => 'nullable|string|in:Não fuma,Ex-fumante,Fuma ocasionalmente,Fumante regular',
            'atividade_fisica' => 'nullable|string|in:Não pratica,1-2 vezes por semana,3-4 vezes por semana,Diariamente',
            'qualidade_sono' => 'nullable|string|in:Ruim,Regular,Bom,Ótimo',
            'observacoes_estilo_vida' => 'nullable|string|max:1000',
            'consumo_alcool' => 'nullable|string|max:255',
        ]);

        // 2. Uso de Transaction blindada
        $paciente = DB::transaction(function () use ($validated) {

            // Salva os dados principais do paciente incluindo o usuário autenticado
            $paciente = Paciente::create([
                'user_id' => auth()->id(), // <--- ADICIONADO: Injeta o ID do usuário logado com segurança
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

            // Contato paciente 
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
                'pais' => $validated['pais'],
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
                'medicacaoEmUso' => $validated['medicacaoEmUso'] ?? null,
                'condicaoCronica' => $validated['condicaoCronica'] ?? null,
                'historicoCirurgico' => $validated['historicoCirurgico'] ?? null,
                'observacaoMedicaGeral' => $validated['observacaoMedicaGeral'] ?? null,
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(), // <--- ADICIONADO: Injeta o ID do usuário logado com segurança
            ]);

            // Cria o Histórico Bucal associando os campos validados corretos
            HistoricoBucal::create([
                'paciente_id' => $paciente->id,
                'ultima_consulta' => $validated['ultima_consulta'] ?? null,
                'motivo_consulta_atual' => $validated['motivo_consulta_atual'] ?? null,
                'sangramento_gengival' => $validated['sangramento_gengival'] ?? null,
                'sensibilidade_dental' => $validated['sensibilidade_dental'] ?? null,
                'uso_proteses_aparelho' => $validated['uso_proteses_aparelho'] ?? null,
                'tipo_aparelho_ou_protese' => $validated['tipo_aparelho_ou_protese'] ?? null,
                'tratamento_bucal_anterior' => $validated['tratamento_bucal_anterior'] ?? null,
                'observacoes_bucal_adicionais' => $validated['observacoes_bucal_adicionais'] ?? null,
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(), // <--- ADICIONADO: Inj
            ]);

            // Cria o registro de Hábitos Saudáveis
            HabitoSaudavel::create([
                'paciente_id' => $paciente->id,
                'frequencia_escovacao' => $validated['frequencia_escovacao'] ?? null,
                'uso_fio_dental' => $validated['uso_fio_dental'] ?? null,
                'habito_fumo' => $validated['habito_fumo'] ?? null,
                'atividade_fisica' => $validated['atividade_fisica'] ?? null,
                'qualidade_sono' => $validated['qualidade_sono'] ?? null,
                'observacoes_estilo_vida' => $validated['observacoes_estilo_vida'] ?? null,
                'consumo_alcool' => $validated['consumo_alcool'] ?? null,
                'paciente_id' => $paciente->id,
                'user_id' => auth()->id(), // <--- ADICIONADO: Inj
            ]);

            return $paciente;
        });

        return redirect()
            ->route('paciente.index')
            ->with('success', 'Paciente cadastrado com sucesso no sistema!');
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
        // dd($paciente->habitoSaudavel->toArray());
        $paciente->load(['enderecoPaciente', 'contatosPaciente', 'historicoBucal', 'historicoMedico', 'habitoSaudavel']);
        return view('dentista.paciente.formulario', compact('paciente'));
    }

    /**
     * Atualiza os dados pessoais e o endereço residencial do paciente.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pacientes,cpf,' . $paciente->id,
            'rg' => 'required|string|max:14',
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'nullable|string|in:Masculino,Feminino,Outro',
            'telefone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',

            // LOCALIZAÇÃO RESIDENCIAL
            'cep' => 'required|string|max:9',
            'pais' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cidade' => 'required|string|max:100',
            'bairro' => 'required|string|max:100',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',

            // HISTÓRICO MÉDICO
            'alergias' => 'nullable|string|max:255',
            'medicacaoEmUso' => 'nullable|string|max:255',
            'condicaoCronica' => 'nullable|string|max:255',
            'historicoCirurgico' => 'nullable|string|max:1000',
            'observacaoMedicaGeral' => 'nullable|string|max:1000',

            // HISTÓRICO BUCAL
            'ultima_consulta' => 'nullable|date|before_or_equal:today',
            'motivo_consulta_atual' => 'nullable|string|max:255',
            'sangramento_gengival' => 'nullable|string|in:Sim,Não',
            'sensibilidade_dental' => 'nullable|string|in:Sim,Não',
            'uso_proteses_aparelho' => 'nullable|string|in:Sim,Não',
            'tipo_aparelho_ou_protese' => 'nullable|string|max:255',
            'tratamento_bucal_anterior' => 'nullable|string|max:1000',
            'observacoes_bucal_adicionais' => 'nullable|string|max:1000',

            // HÁBITOS SAUDÁVEIS
            'frequencia_escovacao' => 'nullable|string|in:1 vez ao dia,2 vezes ao dia,3 ou mais vezes ao dia',
            'uso_fio_dental' => 'nullable|string|in:Sim,Não,Às vezes',
            'habito_fumo' => 'nullable|string|in:Não fuma,Ex-fumante,Fuma ocasionalmente,Fumante regular',
            'atividade_fisica' => 'nullable|string|in:Não pratica,1-2 vezes por semana,3-4 vezes por semana,Diariamente',
            'qualidade_sono' => 'nullable|string|in:Ruim,Regular,Bom,Ótimo',
            'observacoes_estilo_vida' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $paciente) {

            // 1. Atualiza dados principais (tabela pacientes)
            $paciente->update([
                'nome' => $request->nome,
                'sobrenome' => $request->sobrenome,
                'cpf' => $request->cpf,
                'rg' => $request->rg,
                'data_nascimento' => $request->data_nascimento,
                'sexo' => $request->sexo,
            ]);

            // 2. Atualiza ou cria Endereço (tabela endereco_pacientes)
            $paciente->enderecoPaciente()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'cep' => $request->cep,
                    'pais' => $request->pais,
                    'estado' => $request->estado,
                    'cidade' => $request->cidade,
                    'bairro' => $request->bairro,
                    'rua' => $request->rua,
                    'numero' => $request->numero,
                ]
            );

            // 3. Atualiza ou cria Contato (tabela contato_pacientes)
            $paciente->contatosPaciente()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'telefone' => $request->telefone,
                    'whatsapp' => $request->whatsapp,
                    'email' => $request->email,
                ]
            );

            // 4. Repita a lógica para HistoricoMedico, HistoricoBucal, etc.
            // Exemplo para Histórico Médico:
            $paciente->historicoMedico()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'alergias' => $request->alergias,
                    'medicacaoEmUso' => $request->medicacaoEmUso,
                    'condicaoCronica' => $request->condicaoCronica,
                    'historicoCirurgico' => $request->historicoCirurgico,
                    'observacaoMedicaGeral' => $request->observacaoMedicaGeral,
                ]
            );

            $paciente->historicoBucal()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'ultima_consulta' => $request->ultima_consulta,
                    'motivo_consulta_atual' => $request->motivo_consulta_atual,
                    'sangramento_gengival' => $request->sangramento_gengival,
                    'sensibilidade_dental' => $request->sensibilidade_dental,
                    'uso_proteses_aparelho' => $request->uso_proteses_aparelho,
                    'tipo_aparelho_ou_protese' => $request->tipo_aparelho_ou_protese,
                    'tratamento_bucal_anterior' => $request->tratamento_bucal_anterior,
                    'observacoes_bucal_adicionais' => $request->observacoes_bucal_adicionais,
                ]
            );

            $paciente->habitoSaudavel()->updateOrCreate(
                ['paciente_id' => $paciente->id],
                [
                    'frequencia_escovacao' => $request->frequencia_escovacao,
                    'uso_fio_dental' => $request->uso_fio_dental,
                    'habito_fumo' => $request->habito_fumo,
                    'atividade_fisica' => $request->atividade_fisica,
                    'qualidade_sono' => $request->qualidade_sono,
                    'observacoes_estilo_vida' => $request->observacoes_estilo_vida,
                    'consumo_alcool' => $request->consumo_alcool,
                ]
            );
        });

        return redirect()->route('paciente.index')->with('success', 'Paciente atualizado com sucesso!');
    }

    /**
     * Remove ou desativa o paciente, respeitando o histórico legal odontológico.
     */
    public function destroy(Paciente $paciente)
    {
        // Verifica se o paciente possui histórico clínico
        $possuiHistoricoClinico = $paciente->agendamentos()->exists() ||
            $paciente->orcamentos()->exists();

        if ($possuiHistoricoClinico) {

            $paciente->update([
                'status' => 'Inativo'
            ]);

            return redirect()
                ->route('paciente.index')
                ->with('warning', 'O paciente não pode ser excluído porque possui histórico clínico. Seu cadastro foi apenas inativado.');
        }

        DB::transaction(function () use ($paciente) {
            $paciente->endereco()->delete();
            $paciente->contatosPaciente()->delete();
            $paciente->habitoSaudavel()->delete();
            $paciente->historicoBucal()->delete();
            $paciente->delete();
        });

        return redirect()
            ->route('paciente.index')
            ->with('success', 'Paciente excluído com sucesso!');
    }
}