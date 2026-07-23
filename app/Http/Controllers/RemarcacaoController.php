<?php

namespace App\Http\Controllers;

use App\Models\Remarcacao;
use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RemarcacaoController extends Controller // ✅ Nome correto da classe
{
    public function index(Request $request)
    {
        // 🔒 Filtra as remarcações estritamente pelo usuário autenticado
        $query = Remarcacao::with(['agendamento.paciente', 'usuario'])
            ->where('user_id', auth()->id())
            ->latest();

        $termo = $request->input('busca');

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($termo) {
                $q->whereHas('agendamento.paciente', function ($subQ) use ($termo) {
                    $subQ->where('nome', 'LIKE', "%{$termo}%")
                        ->orWhere('sobrenome', 'LIKE', "%{$termo}%");
                })->orWhereDate('data_nova', $termo);
            });
        }

        $remarcacoes = $query->paginate(15);
        $remarcacoes->appends(['busca' => $termo ?? '']);

        return view('dentista.remarcacao.index', compact('remarcacoes'));
    }

    public function create(Request $request)
    {
        // 🔒 Busca apenas os agendamentos pendentes ou confirmados do usuário logado
        $agendamentos = Agendamento::with('paciente')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['AGENDADO', 'CONFIRMADO'])
            ->orderBy('data', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        $agendamento = null;

        if ($request->filled('agendamento_id')) {
            // 🔒 Garante que o agendamento selecionado via parâmetro pertence ao usuário logado
            $agendamento = Agendamento::with('paciente')
                ->where('user_id', auth()->id())
                ->findOrFail($request->agendamento_id);

            // 🛑 Verifica se o agendamento está cancelado
            if ($agendamento->status === 'CANCELADO') {
                return redirect()
                    ->back()
                    ->with('error', 'Não é possível remarcar um agendamento que já está cancelado.');
            }
        }

        return view('dentista.remarcacao.formulario', compact('agendamento', 'agendamentos'));
    }

    public function store(Request $request)
    {
        // 1. Validação dos campos enviados pelo formulário
        $request->validate([
            'agendamento_id'     => 'required|exists:agendamentos,id',
            'data_anterior'      => 'required|date',
            'data_nova'          => 'required|date|after_or_equal:today',
            'hora_inicio_nova'   => 'required',
            'hora_fim_nova'      => 'required|after:hora_inicio_nova',
            'motivo'             => 'nullable|max:255',
        ]);

        // 2. Busca o agendamento pertencente ao usuário autenticado
        $agendamento = Agendamento::where('user_id', auth()->id())->findOrFail($request->agendamento_id);

        // 🛑 Garante que não vai remarcar um agendamento cancelado
        if ($agendamento->status === 'CANCELADO') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Não é possível remarcar um agendamento cancelado.');
        }

        // 3. Atualiza os dados do agendamento com a nova data e horários
        $agendamento->update([
            'data'        => $request->data_nova,
            'hora_inicio' => $request->hora_inicio_nova,
            'hora_fim'    => $request->hora_fim_nova,
            // Se sua tabela tiver o campo status ou observação para histórico, pode atualizar aqui também:
            // 'observacao' => 'Remarcado: ' . $request->motivo,
        ]);

        // 4. (Opcional) Se você tiver a model Remarcacao para salvar o histórico, salve aqui:
        // Remarcacao::create([
        //     'agendamento_id' => $agendamento->id,
        //     'user_id'        => auth()->id(),
        //     'data_anterior'  => $request->data_anterior,
        //     'data_nova'      => $request->data_nova,
        //     'motivo'         => $request->motivo,
        // ]);

        // 5. Redireciona de volta com mensagem de sucesso
        return redirect()
            ->route('agendamento.index') // Ou route('remarcacao.index') dependendo de onde prefere cair
            ->with('success', 'Agendamento remarcado com sucesso!');
    }

    public function show(Remarcacao $remarcacao)
    {
        $remarcacao->load(['agendamento.paciente', 'usuario']);
        return view('dentista.remarcacao.ver', compact('remarcacao'));
    }

    public function destroy(Remarcacao $remarcacao)
    {
        $remarcacao->delete();
        return redirect()->route('remarcacao.index')
            ->with('sucesso', 'Registro excluído!');
    }
}
