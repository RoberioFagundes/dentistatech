<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\FilaAgendamento;
use App\Models\Paciente;
use App\Models\FilaAtendimento;
use App\Models\Agendamento;

class FilaAgendamentoEsperaController extends Controller
{
    public function index(Request $request)
    {
        $nome = $request->input('nome');

        // Atualizado para 'paciente'
        $query = FilaAgendamento::with('paciente')
            ->where('user_id', auth()->id());

        if ($nome) {
            $query->whereHas('paciente', function ($q) use ($nome) {
                $q->where('nome', 'like', "%{$nome}%");
            });
        }

        $filas = $query->orderByRaw('CASE WHEN dias_urgencia > 0 THEN 0 ELSE 1 END')
            ->orderBy('dias_urgencia', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('dentista.filaesperaagendamento.index', compact('filas', 'nome'));
    }

    public function atualizarParecer(Request $request, $id)
    {
        $request->validate([
            'parecer_telefonico' => 'nullable|string|max:1000'
        ]);

        $fila = FilaAgendamento::findOrFail($id);

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
        $userId = auth()->id();
        $pacientes = Paciente::where('user_id', $userId)->get();

        // Se você precisa exibir atendimentos de hoje, use outro nome de variável
        $atendimentosHoje = FilaAtendimento::where('user_id', $userId)
            ->whereDate('chegada_em', today())
            ->get();

        return view('dentista.filaesperaagendamento.formulario', [
            'pacientes' => $pacientes,
            'fila' => null, // Deixe nulo para o formulário entender que é uma criação
            'atendimentosHoje' => $atendimentosHoje // Use um nome diferente aqui
        ]);
    }

    public function storeFilaEsperaAgendamento(Request $request)
    {
        $validated = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'dias_urgencia'  => 'nullable|integer|min:0',
            'motivo'         => 'required|string|max:255',
            'arquivoInput'   => 'required_with:dias_urgencia|nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            $caminhoArquivo = null;
            if ($request->hasFile('arquivoInput')) {
                $caminhoArquivo = $request->file('arquivoInput')->store('anexos_fila', 'public');
            }

            FilaAgendamento::create([
                'paciente_id'     => $validated['paciente_id'],
                'dias_urgencia' => $validated['dias_urgencia'] ?? 0,
                'motivo'        => $validated['motivo'],
                'arquivo_path'  => $caminhoArquivo,
                'status'        => 'pendente',
                'user_id'       => auth()->id(),
            ]);

            return redirect()->route('filaAgendamentoEspera.index')->with('success', 'Adicionado à fila com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['erro' => 'Erro ao salvar: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteFilaEsperaAgendamento($id)
    {
        $fila = FilaAgendamento::findOrFail($id);
        $fila->delete();

        return redirect()
            ->route('FilaEspera.index')
            ->with('sucesso-agendamentos', 'Fila de Espera de Agendamento removida com sucesso!');
    }




    public function updateFilaEsperaAgendamento(Request $request, $id)
    {
        $fila = FilaAgendamento::findOrFail($id);

        $validated = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'dias_urgencia'  => 'nullable|integer|min:0',
            'motivo'         => 'required|string|max:255',
            'arquivoInput'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            // Se um novo arquivo for enviado, deletamos o antigo e salvamos o novo
            if ($request->hasFile('arquivoInput')) {
                if ($fila->arquivo_path) {
                    \Storage::disk('public')->delete($fila->arquivo_path);
                }
                $fila->arquivo_path = $request->file('arquivoInput')->store('anexos_fila', 'public');
            }

            $fila->update([
                'paciente_id'     => $validated['paciente_id'],
                'dias_urgencia' => $validated['dias_urgencia'] ?? 0,
                'motivo'        => $validated['motivo'],
                'arquivo_path'  => $fila->arquivo_path, // Mantém o caminho atualizado
            ]);

            return redirect()->route('filaAgendamentoEspera.index')->with('success', 'Registro atualizado!');
        } catch (\Exception $e) {
            return back()->withErrors(['erro' => 'Erro ao atualizar: ' . $e->getMessage()])->withInput();
        }
    }

    public function adicionar(Paciente $pessoa)
    {
        $jaExiste = FilaAgendamento::where('paciente_id', $pessoa->id)
            ->where('status', 'pendente')
            ->exists();

        if ($jaExiste) {
            return back()->withErrors([
                'erro' => 'Esta pessoa já está na fila de espera.'
            ]);
        }

        FilaAgendamento::create([
            'paciente_id' => $pessoa->id,
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


        $fila = FilaAgendamento::whereIn('id', $idsSelecionados)
            ->with('paciente')
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
                'data' => $dataPronta->format('Y-m-d H:i:s'),
                'paciente_id' => $item->paciente->id,
                'observacao' => $motivos[$item->id] ?? null,
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

        FilaAgendamento::whereIn('id', $idsSelecionados)->delete();

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
        $fila = FilaAgendamento::findOrFail($id);
        $pacientes = Paciente::where('user_id', auth()->id())->get();

        return view('seu.caminho.formulario', compact('fila', 'pacientes'));
    }

    public function update(Request $request, $id)
    {
        $fila = FilaAgendamento::findOrFail($id);

        $validated = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'dias_urgencia'  => 'nullable|integer|min:0',
            'motivo'         => 'required|string|max:255',
            'arquivoInput'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            // Se um novo arquivo for enviado, deletamos o antigo e salvamos o novo
            if ($request->hasFile('arquivoInput')) {
                if ($fila->arquivo_path) {
                    \Storage::disk('public')->delete($fila->arquivo_path);
                }
                $fila->arquivo_path = $request->file('arquivoInput')->store('anexos_fila', 'public');
            }

            $fila->update([
                'paciente_id'     => $validated['paciente_id'],
                'dias_urgencia' => $validated['dias_urgencia'] ?? 0,
                'motivo'        => $validated['motivo'],
                'arquivo_path'  => $fila->arquivo_path, // Mantém o caminho atualizado
            ]);

            return redirect()->route('filaAgendamentoEspera.index')->with('success', 'Registro atualizado!');
        } catch (\Exception $e) {
            return back()->withErrors(['erro' => 'Erro ao atualizar: ' . $e->getMessage()])->withInput();
        }
    }
}
