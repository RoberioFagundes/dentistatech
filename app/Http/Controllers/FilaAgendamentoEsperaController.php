<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\FilaEsperaAgendamento;
use App\Models\Paciente;
use App\Models\EnderecoCidadao;
use App\Models\FilaAtendimento;
use App\Models\Agendamento;

class FilaAgendamentoEsperaController extends Controller
{
    public function index(Request $request)
    {
        $nome = $request->input('nome');
        $bairro = $request->input('bairro');
        $motivo = $request->input('motivo');
        $created_at = $request->input('created_at');

        $FilaEspera = FilaAgendamentoEspera::with([
            'pessoa' => function ($q) {
                $q->withCount(['agendamentos', 'atendimentos']);
            }
        ])
            ->where('user_id', auth()->id())

            // 1º CRITÉRIO: Agrupa por blocos de real urgência.
            // Se tiver dias cadastrados (> 0), recebe peso 0 (vai para o topo).
            // Se for 0 ou NULL, recebe peso 1 (vai para o final/posição normal dele).
            ->orderByRaw('CASE WHEN dias_urgencia > 0 THEN 0 ELSE 1 END')

            // 2º CRITÉRIO: Dentro do grupo do topo (> 0), ordena do menor para o maior (1, 2, 3...)
            ->orderBy('dias_urgencia', 'asc')

            // 3º CRITÉRIO: DESEMPATE CRUCIAL. Ordena pelo ID (ou data de criação original).
            // Como o ID não muda quando você edita, o Robério (que agora tem 0 dias) 
            // continuará exatamente na mesma posição 4 que estava antes.
            ->orderBy('id', 'asc')

            // FILTRO POR NOME
            ->when($nome, function ($query) use ($nome) {
                $query->whereHas('pessoa', function ($q) use ($nome) {
                    $q->where('nome', 'like', "%{$nome}%");
                });
            })

            // FILTRO POR BAIRRO
            ->when($bairro, function ($query) use ($bairro) {
                $query->whereHas('pessoa.endereco', function ($q) use ($bairro) {
                    $q->where('bairro', 'like', "%{$bairro}%");
                });
            })

            // FILTRO POR MOTIVO
            ->when($motivo, function ($query) use ($motivo) {
                $query->where('motivo', 'like', "%{$motivo}%");
            })

            // FILTRO POR DATA DE CRIAÇÃO
            ->when($created_at, function ($query) use ($created_at) {
                $query->whereDate('created_at', $created_at);
            })

            ->paginate(10)
            ->withQueryString();

        // Adiciona posição considerando a paginação
        $FilaEspera->getCollection()->transform(function ($item, $key) use ($FilaEspera) {
            $item->posicao_fila = (($FilaEspera->currentPage() - 1) * $FilaEspera->perPage()) + $key + 1;
            return $item;
        });

        return view(
            'secretaria.sistema.fila_espera_agendamento.index',
            compact('FilaEspera', 'nome', 'bairro', 'motivo', 'created_at')
        );
    }

    public function atualizarParecer(Request $request, $id)
    {
        $request->validate([
            'parecer_telefonico' => 'nullable|string|max:1000'
        ]);

        $fila = FilaEsperaAgendamento::findOrFail($id);

        $fila->update([
            'parecer_telefonico' => $request->parecer_telefonico
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parecer atualizado com sucesso!'
        ]);
    }
    public function create()
    {
        // Obtém o ID do usuário logado (ou da clínica/dentista vinculado a ele)
        $userId = auth()->id();

        // Filtra os dados apenas para o usuário logado
        // Isso evita que um dentista veja dados de outra clínica
        $pacientes =Paciente::where('user_id', $userId)->get();
        
        // Se você não precisa da fila inteira no formulário, remova essas linhas.
        // Carregar .all() desnecessariamente consome muita memória.
        $fila = FilaAtendimento::where('user_id', $userId)
                                ->whereDate('chegada_em', today())
                                ->get();

        // Se o seu formulário é para criar, não faz sentido carregar 
        // a própria FilaEsperaAgendamento::all() dentro dele.
        
        return view('dentista.filaesperaagendamento.formulario', compact('pacientes', 'fila'));

    }

    public function storeFilaEsperaAgendamento(Request $request)
    {
        // 1️⃣ Validação (Adicionada a regra condicional do arquivo se houver urgência)
        $validated = $request->validate([
            'nomeInput' => 'required|string|max:255',
            'cpfInput' => 'nullable|string|max:14',
            'telefoneInput' => 'nullable|string|max:20',
            'estadoInput' => 'required|string|max:50',
            'cidadeInput' => 'required|string|max:100',
            'bairroInput' => 'required|string|max:100',
            'ruaInput' => 'required|string|max:255',
            'motivo' => 'required|string',
            'motivo_outro' => 'required_if:motivo,Outro|nullable|string|max:255',
            'diasUrgenciaInput' => 'nullable|integer|min:0',
            'arquivoInput' => 'required_with:diasUrgenciaInput|nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ], [
            'arquivoInput.required_with' => 'É obrigatório anexar um documento quando os dias de urgência são informados.',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->withErrors('Você precisa estar logado para cadastrar uma pessoa.');
        }

        try {
            DB::beginTransaction();

            $pessoa = null;

            if (!empty($validated['cpfInput'])) {
                // Guarda o CPF exatamente como veio do formulário (com máscara)
                $cpfComMascara = $validated['cpfInput'];
                // Gera uma versão limpa apenas para garantir a busca estruturada
                $cpfLimpo = preg_replace('/\D/', '', $cpfComMascara);

                // 2️⃣ Busca inteligente: tenta achar o CPF com ou sem máscara para não duplicar
                $pessoa = Paciente::where('cpf', $cpfComMascara)
                    ->orWhere('cpf', $cpfLimpo)
                    ->first();
            } else {
                // Caso não tenha informado CPF, busca por Telefone + Nome exato
                $pessoa = Paciente::where('telefone', $validated['telefoneInput'])
                    ->where('nome', $validated['nomeInput'])
                    ->first();
            }

            // 3️⃣ Condicional de Criação: SÓ cria se NÃO encontrar
            if (!$pessoa) {
                // Cria um novo registro salvando o CPF COM MÁSCARA
                $pessoa = Paciente::create([
                    'nome' => $validated['nomeInput'],
                    'cpf' => $validated['cpfInput'] ?? null, // 👈 Salva com a máscara aqui
                    'telefone' => $validated['telefoneInput'],
                    'user_id' => auth()->id(),
                ]);

                // Cria o endereço atrelado a essa nova pessoa
                EnderecoCidadao::create([
                    'estado' => $validated['estadoInput'],
                    'cidade' => $validated['cidadeInput'],
                    'bairro' => $validated['bairroInput'],
                    'rua' => $validated['ruaInput'],
                    'pessoa_id' => $pessoa->id,
                ]);
            }

            // 4️⃣ Tratamento do Motivo Customizado ("Outro")
            $motivoFinal = $validated['motivo'] === 'Outro' ? $validated['motivo_outro'] : $validated['motivo'];

            // 5️⃣ Tratamento de Upload de Arquivo
            $caminhoArquivo = null;
            if ($request->hasFile('arquivoInput')) {
                $caminhoArquivo = $request->file('arquivoInput')->store('anexos_fila', 'public');
            }

            // 6️⃣ Cria SEMPRE o registro na Fila de Espera
            FilaEsperaAgendamento::create([
                'pessoa_id' => $pessoa->id,
                'motivo' => $motivoFinal,
                'status' => 'pendente',
                'dias_urgencia' => $validated['diasUrgenciaInput'] ?? null,
                'arquivo_path' => $caminhoArquivo,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('FilaEspera.index')
                ->with('sucesso-agendamentos', 'Agendamento registrado na fila de espera com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($caminhoArquivo) && Storage::disk('public')->exists($caminhoArquivo)) {
                Storage::disk('public')->delete($caminhoArquivo);
            }

            report($e);
            return back()->withErrors(['erro' => 'Erro ao salvar o agendamento na lista de espera: ' . $e->getMessage()])->withInput();
        }
    }
    public function deleteFilaEsperaAgendamento($id)
    {
        $fila = FilaEsperaAgendamento::findOrFail($id);
        $fila->delete();

        return redirect()
            ->route('FilaEspera.index')
            ->with('sucesso-agendamentos', 'Fila de Espera de Agendamento removida com sucesso!');
    }




    public function updateFilaEsperaAgendamento(Request $request, $id)
    {
        $fila = FilaEsperaAgendamento::findOrFail($id);

        // 1. Validação dos dados
        $validated = $request->validate([
            'nomeInput' => 'required|string|max:255',
            'cpfInput' => 'nullable|string|max:14',
            'telefoneInput' => 'nullable|string|max:20',
            'estadoInput' => 'required|string|max:50',
            'cidadeInput' => 'required|string|max:100',
            'bairroInput' => 'required|string|max:100',
            'ruaInput' => 'required|string|max:255',
            'numeroInput' => 'nullable|string|max:20',
            'complementoInput' => 'nullable|string|max:255',
            'motivoInput' => 'required|string|max:255',
            'diasUrgenciaInput' => 'nullable|integer|min:0', // Permite nulo ou números positivos
            'documentoInput' => [
                $request->input('diasUrgenciaInput') > 0 && empty($fila->arquivo_path) ? 'required' : 'nullable',
                'file',
                'mimes:pdf,png,jpg,jpeg',
                'max:2048'
            ],
        ]);

        // 2. Tratamento do Upload do Documento
        if ($request->hasFile('documentoInput') && $request->file('documentoInput')->isValid()) {
            if (!empty($fila->arquivo_path) && Storage::disk('public')->exists($fila->arquivo_path)) {
                Storage::disk('public')->delete($fila->arquivo_path);
            }
            $path = $request->file('documentoInput')->store('documentos_fila', 'public');
            $fila->arquivo_path = $path;
        }

        // 3. Atualiza os dados da Fila de Espera (MODIFICADO AQUI ⚠️)
        $fila->update([
            'motivo' => $validated['motivoInput'],
            // Tiramos o "?? 0". Se vier em branco no formulário, salva como NULL no banco de dados.
            'dias_urgencia' => $validated['diasUrgenciaInput'],
            'arquivo_path' => $fila->arquivo_path,
        ]);

        // 4. Atualiza os dados da Paciente
        $fila->pessoa->update([
            'nome' => $validated['nomeInput'],
            'cpf' => $validated['cpfInput'],
            'telefone' => $validated['telefoneInput'],
        ]);

        // 5. Atualiza o Endereço
        $fila->pessoa->endereco_cidadaos->update([
            'estado' => $validated['estadoInput'],
            'cidade' => $validated['cidadeInput'],
            'bairro' => $validated['bairroInput'],
            'rua' => $validated['ruaInput'],
            'numero' => $validated['numeroInput'],
            'complemento' => $validated['complementoInput'],
        ]);

        return redirect()->route('filaAgendamentoEspera.index')->with('sucesso', 'Registro atualizado com sucesso!');
    }

    public function adicionar(Paciente $pessoa)
    {
        $jaExiste = FilaEsperaAgendamento::where('pessoa_id', $pessoa->id)
            ->where('status', 'pendente')
            ->exists();

        if ($jaExiste) {
            return back()->withErrors([
                'erro' => 'Esta pessoa já está na fila de espera.'
            ]);
        }

        FilaEsperaAgendamento::create([
            'pessoa_id' => $pessoa->id,
            'motivo' => 'Inclusão manual',
            'status' => 'pendente',
            'agendamento_id' => null,
            'user_id' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'Paciente adicionada à fila de espera com sucesso.'
        );
    }




    public function agendarSelecionados(Request $request)
    {
        // 🔎 Se quiser apenas inspecionar o que está vindo, use:
        // \Log::info('Requisição recebida:', $request->all());

        // dd($request->all());
        $validated = $request->validate([
            'selecionados' => 'required|array|min:1',
            'datas' => 'required|array',
            'datas.*' => 'required|date_format:Y-m-d\TH:i',
            'motivos' => 'array', // ✅ adiciona aqui
            'motivos.*' => 'nullable|string|max:255', // ✅ cada motivo é texto opcional

        ], [
            'selecionados.required' => 'Selecione pelo menos uma pessoa para agendar.',
            'datas.*.required' => 'Informe a data e hora para todos os selecionados.',
            'datas.*.date_format' => 'Formato de data/hora inválido. Use o seletor corretamente.',
        ]);

        $idsSelecionados = $validated['selecionados'];
        $datasAgendamento = $validated['datas'];
        $motivos = $validated['motivos'] ?? [];

        // DB::beginTransaction();


        $fila = FilaEsperaAgendamento::whereIn('id', $idsSelecionados)
            ->with('pessoa')
            ->get();

        if ($fila->isEmpty()) {
            return back()->withErrors(['error' => 'Nenhum registro válido encontrado para agendar.']);
        }

        $agendamentos = [];
        $errosPorItem = [];

        foreach ($fila as $item) {
            $dataBruta = $datasAgendamento[$item->id] ?? null;
            if (empty($dataBruta)) {
                $errosPorItem[$item->id] = 'Data/hora não informada.';
                continue;
            }

            try {
                $dataPronta = Carbon::createFromFormat('Y-m-d\TH:i', $dataBruta);
            } catch (\Exception $e) {
                $errosPorItem[$item->id] = 'Formato de data inválido.';
                continue;
            }

            if ($dataPronta->isWeekend()) {
                $errosPorItem[$item->id] = 'Data em fim de semana não permitida.';
                continue;
            }

            if (DB::table('feriados')->whereDate('data', $dataPronta)->exists()) {
                $errosPorItem[$item->id] = 'Data em feriado não permitida.';
                continue;
            }

            $agendamentos[] = [
                'data_hora' => $dataPronta->format('Y-m-d H:i:s'),
                'pessoa_id' => $item->pessoa->id,
                'observacoes' => $motivos[$item->id] ?? null,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

        }

        if (count($agendamentos) === 0) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Nenhum agendamento válido foi encontrado.']);
        }

        Agendamento::insert($agendamentos);

        FilaEsperaAgendamento::whereIn('id', $idsSelecionados)->delete();

        DB::commit();

        $msg = 'Agendamentos criados com sucesso!';
        if (!empty($errosPorItem)) {
            $msg .= ' Alguns não foram agendados: ' . collect($errosPorItem)
                ->map(fn($v, $k) => "ID {$k}: {$v}")
                ->implode(' | ');
        }

        return redirect()->back()->with('sucesso-agendamentos', $msg);

    }


    public function edit($id)
    {
        $filaAgendamentoEspera = FilaEsperaAgendamento::with(['pessoa.endereco'])
            ->findOrFail($id);

        return view('secretaria.sistema.fila_espera_agendamento.editar', compact('filaAgendamentoEspera'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomeInput' => 'required|string|max:255',
            'cpfInput' => 'nullable|string|max:14',
            'telefoneInput' => 'nullable|string|max:20',
            'estadoInput' => 'required|string|max:50',
            'cidadeInput' => 'required|string|max:100',
            'bairroInput' => 'required|string|max:100',
            'ruaInput' => 'required|string|max:255',
            'motivoInput' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $fila = FilaEsperaAgendamento::with(['pessoa.endereco'])->findOrFail($id);

            // Atualiza o motivo
            $fila->update([
                'motivo' => $validated['motivoInput'],
            ]);

            // Atualiza dados pessoais
            $fila->pessoa->update([
                'nome' => $validated['nomeInput'],
                'cpf' => $validated['cpfInput'],
                'telefone' => $validated['telefoneInput'],
            ]);

            // Atualiza endereço
            $fila->pessoa->endereco->update([
                'estado' => $validated['estadoInput'],
                'cidade' => $validated['cidadeInput'],
                'bairro' => $validated['bairroInput'],
                'rua' => $validated['ruaInput'],
            ]);
        });

        return redirect()
            ->route('filaAgendamentoEspera.index')
            ->with('sucesso', 'Registro atualizado com sucesso!');
    }

}
