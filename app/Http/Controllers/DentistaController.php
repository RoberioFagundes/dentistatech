<?php

namespace App\Http\Controllers;

use App\Models\Dentista;
use App\Models\EnderecoDentista;
use App\Models\ContatoDentista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('especialidade', 'like', "%{$busca}%");
            });
        }

        // Traz os dentistas paginados com a contagem de contatos cadastrados
        $dentistas = $query->withCount('contatos')
            ->orderBy('nome', 'asc')
            ->paginate(10);

        return view('dentista.dentista.index', compact('dentistas'));
        // Se usar Blade: return view('dentistas.index', compact('dentistas'));
    }

    /**
     * Exibe o formulário de criação (Útil caso use views Blade).
     */
    public function create()
    {
        return view('dentista.dentista.formulario');
    }

    /**
     * Salva um novo dentista no banco de dados.
     */


    public function store(Request $request)
    {
        // 1. Validação unificada (Adicionado o 'cep' que faltava)
        $dadosValidados = $request->validate([
            // Dados do Dentista
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cro' => 'required|string|max:20|unique:dentistas,cro',
            'cro_uf' => 'required|string|size:2',
            'especialidade' => 'required|string|max:150',

            // Dados do Endereço
            'cep' => 'required|string|max:10', // <-- Adicionado aqui!
            'pais' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'rua' => 'required|string|max:255',
            'numero' => 'required|integer',

            // Dados de Contato
            'telefone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:contato_dentistas,email',
        ]);

        // 2. Transaction para garantir consistência dos dados
        $dentista = DB::transaction(function () use ($dadosValidados) {

            // Cria o dentista vinculando o usuário autenticado
            $dentista = Dentista::create([
                'user_id'       => auth()->id(), // <-- ID do usuário logado mantido com segurança
                'nome'          => $dadosValidados['nome'],
                'sobrenome'     => $dadosValidados['sobrenome'],
                'cro'           => $dadosValidados['cro'],
                'cro_uf'        => $dadosValidados['cro_uf'],
                'especialidade' => $dadosValidados['especialidade'],
            ]);

            // Vincula e salva o Endereço utilizando os dados validados
            $dentista->endereco()->create([
                'cep'    => $dadosValidados['cep'],
                'pais'   => $dadosValidados['pais'], // Removi o acento, ajuste se sua coluna tiver 'país'
                'estado' => $dadosValidados['estado'],
                'cidade' => $dadosValidados['cidade'],
                'bairro' => $dadosValidados['bairro'],
                'rua'    => $dadosValidados['rua'],
                'numero' => $dadosValidados['numero'],
            ]);

            // Vincula e salva os Contatos utilizando os dados validados
            $dentista->contatos()->create([
                'telefone' => $dadosValidados['telefone'],
                'whatsapp' => $dadosValidados['whatsapp'],
                'email'    => $dadosValidados['email'],
            ]);

            return $dentista;
        });

        // 3. Redirecionamento com mensagem de sucesso
        return redirect()
            ->route('dentista.index')
            ->with('success', 'Perfil de dentista vinculado e cadastrado com sucesso!');
    }
    /**
     * Exibe o perfil detalhado de um dentista específico (incluindo contatos e endereço).
     */
    public function show(Dentista $dentista)
    {
        // Carrega os relacionamentos através do Route Model Binding do Laravel
        $dentista->load(['contatos', 'endereco']);

        return view('dentista.dentista.show', compact('dentista'));
    }

    /**
     * Exibe o formulário de edição pré-carregando os dados do profissional.
     */
    public function edit($id)
    {
        // Busca o dentista pelo ID bruto enviado pela URL (ex: /dentista/1/edit)
        $dentista = Dentista::findOrFail($id);

        return view('dentista.dentista.formulario', compact('dentista'));
    }

    /**
     * Atualiza os dados cadastrais do dentista.
     */
   public function update(Request $request, $id)
{
    // 1. Busca o dentista pelo ID bruto enviado pela rota
    $dentista = Dentista::findOrFail($id);

    // 2. Validação com as regras corretas (incluindo os campos de contato e endereço do formulário)
    $dadosValidados = $request->validate([
        'nome'          => 'required|string|max:255',
        'sobrenome'     => 'required|string|max:255',
        'cro'           => 'required|string|max:20|unique:dentistas,cro,' . $dentista->id, // Ignora o próprio ID
        'cro_uf'        => 'required|string|size:2',
        'especialidade' => 'required|string|max:150',
        'telefone'      => 'required|string|max:20',
        'whatsapp'      => 'required|string|max:20',
        'email'         => 'required|email|max:255',
        'cep'           => 'required|string|max:10',
        'pais'          => 'required|string|max:100',
        'estado'        => 'required|string|max:100',
        'cidade'        => 'required|string|max:100',
        'bairro'        => 'required|string|max:100',
        'rua'           => 'required|string|max:255',
        'numero'        => 'required|integer',
    ]);

    // 3. Atualiza os dados no banco
    $dentista->update($dadosValidados);

    // 4. Redireciona de volta para a listagem com mensagem de sucesso
    return redirect()->route('dentista.index')->with('success', 'Cadastro do dentista atualizado com sucesso!');
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

            return redirect()->route('dentista.index')->with('success', 'Dentista desativado com sucesso!');
        }

        // Se não houver histórico atrelado, a exclusão física é liberada
        $dentista->delete();

        return redirect()->route('dentista.index')->with('success', 'Dentista removido com sucesso!');
    }
}