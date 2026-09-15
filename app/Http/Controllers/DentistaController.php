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
        $query = Dentista::where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->whereRaw("CONCAT(nome, ' ', sobrenome) LIKE ?", ["%{$busca}%"])
                    ->orWhere('nome', 'like', "%{$busca}%")
                    ->orWhere('sobrenome', 'like', "%{$busca}%")
                    ->orWhere('especialidade', 'like', "%{$busca}%")
                    ->orWhere('cro', 'like', "%{$busca}%");
            });
        }

        $dentistas = $query
            ->withCount('contatos')
            ->orderBy('nome', 'asc')
            ->paginate(10);

        $dentistas->appends($request->all());

        return view('dentista.dentista.index', compact('dentistas'));
    }

    /**
     * Exibe o formulário de criação.
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
        $dadosValidados = $request->validate([
            'nome' => 'required|string|max:255',
            'sobrenome' => 'required|string|max:255',
            'cro' => 'required|string|max:20|unique:dentistas,cro',
            'cro_uf' => 'required|string|size:2',
            'especialidade' => 'required|string|max:150',

            'cep' => 'required|string|max:10',
            'pais' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'rua' => 'required|string|max:255',
            'numero' => 'required|integer',

            'telefone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:contato_dentistas,email',
        ]);

        DB::transaction(function () use ($dadosValidados) {
            $dentista = Dentista::create([
                'user_id'       => auth()->id(),
                'nome'          => $dadosValidados['nome'],
                'sobrenome'     => $dadosValidados['sobrenome'],
                'cro'           => $dadosValidados['cro'],
                'cro_uf'        => $dadosValidados['cro_uf'],
                'especialidade' => $dadosValidados['especialidade'],
            ]);

            $dentista->endereco()->create([
                'cep'    => $dadosValidados['cep'],
                'pais'   => $dadosValidados['pais'],
                'estado' => $dadosValidados['estado'],
                'cidade' => $dadosValidados['cidade'],
                'bairro' => $dadosValidados['bairro'],
                'rua'    => $dadosValidados['rua'],
                'numero' => $dadosValidados['numero'],
            ]);

            $dentista->contatos()->create([
                'telefone' => $dadosValidados['telefone'],
                'whatsapp' => $dadosValidados['whatsapp'],
                'email'    => $dadosValidados['email'],
            ]);
        });

        return redirect()
            ->route('dentista.index')
            ->with('success', 'Perfil de dentista vinculado e cadastrado com sucesso!');
    }

    /**
     * Exibe o perfil detalhado de um dentista específico.
     */
    public function show(Dentista $dentista)
    {
        $dentista->load(['contatos', 'endereco']);

        return view('dentista.dentista.show', compact('dentista'));
    }

    /**
     * Exibe o formulário de edição pré-carregando os dados.
     */
    public function edit($id)
    {
        $dentista = Dentista::with(['contatos', 'endereco'])->findOrFail($id);

        return view('dentista.dentista.formulario', compact('dentista'));
    }

    /**
     * Atualiza os dados cadastrais do dentista, endereço e contatos.
     */
    public function update(Request $request, $id)
    {
        $dentista = Dentista::with(['contatos', 'endereco'])->findOrFail($id);

        $dadosValidados = $request->validate([
            'nome'          => 'required|string|max:255',
            'sobrenome'     => 'required|string|max:255',
            'cro'           => 'required|string|max:20|unique:dentistas,cro,' . $dentista->id,
            'cro_uf'        => 'required|string|size:2',
            'especialidade' => 'required|string|max:150',
            
            'cep'           => 'required|string|max:10',
            'pais'          => 'required|string|max:100',
            'estado'        => 'required|string|max:100',
            'cidade'        => 'required|string|max:100',
            'bairro'        => 'required|string|max:100',
            'rua'           => 'required|string|max:255',
            'numero'        => 'required|integer',

            'telefone'      => 'required|string|max:20',
            'whatsapp'      => 'required|string|max:20',
            // Ignora o id do contato atual na validação de unique do email
            'email'         => 'required|email|max:255|unique:contato_dentistas,email,' . optional($dentista->contatos)->id,
        ]);

        DB::transaction(function () use ($dentista, $dadosValidados) {
            // 1. Atualiza dados principais do dentista
            $dentista->update([
                'nome'          => $dadosValidados['nome'],
                'sobrenome'     => $dadosValidados['sobrenome'],
                'cro'           => $dadosValidados['cro'],
                'cro_uf'        => $dadosValidados['cro_uf'],
                'especialidade' => $dadosValidados['especialidade'],
            ]);

            // 2. Atualiza ou cria o endereço vinculado
            $dentista->endereco()->updateOrCreate(
                ['dentista_id' => $dentista->id],
                [
                    'cep'    => $dadosValidados['cep'],
                    'pais'   => $dadosValidados['pais'],
                    'estado' => $dadosValidados['estado'],
                    'cidade' => $dadosValidados['cidade'],
                    'bairro' => $dadosValidados['bairro'],
                    'rua'    => $dadosValidados['rua'],
                    'numero' => $dadosValidados['numero'],
                ]
            );

            // 3. Atualiza ou cria o contato vinculado
            $dentista->contatos()->updateOrCreate(
                ['dentista_id' => $dentista->id],
                [
                    'telefone' => $dadosValidados['telefone'],
                    'whatsapp' => $dadosValidados['whatsapp'],
                    'email'    => $dadosValidados['email'],
                ]
            );
        });

        return redirect()->route('dentista.index')->with('success', 'Cadastro do dentista atualizado com sucesso!');
    }

    /**
     * Remove ou desativa o dentista do sistema.
     */
    public function destroy($id)
    {
        try {
            $dentista = Dentista::findOrFail($id);

            // ⛔ BLOQUEIA SE TIVER HISTÓRICO IMPORTANTE (caso os relacionamentos existam)
            if (method_exists($dentista, 'agendamentos') && $dentista->agendamentos()->exists()) {
                return redirect()->route('dentista.index')
                    ->with('erro', '❌ Não pode apagar: existem agendamentos vinculados!');
            }

            DB::transaction(function () use ($dentista) {
                // ✅ APAGA OS RELACIONAMENTOS COM SEGURANÇA
                $dentista->contatos()->delete();
                $dentista->endereco()->delete();

                // ✅ AGORA APAGA O DENTISTA
                $dentista->delete();
            });

            return redirect()->route('dentista.index')
                ->with('success', '✅ APAGADO DO BANCO COM SUCESSO!');

        } catch (\Exception $e) {
            return redirect()->route('dentista.index')
                ->with('erro', '🚨 ERRO: ' . $e->getMessage());
        }
    }
}