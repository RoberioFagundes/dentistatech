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
        $query = Remarcacao::with(['agendamento.paciente', 'usuario'])->latest();

        if ($request->filled('busca')) {
            $termo = $request->input('busca');
            $query->whereHas('agendamento.paciente', function ($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                    ->orWhere('sobrenome', 'LIKE', "%{$termo}%");
            })->orWhereDate('data_nova', $termo);
        }

        $remarcacoes = $query->paginate(15);
        $remarcacoes->appends(['busca' => $termo ?? '']);

        return view('dentista.remarcacao.index', compact('remarcacoes'));
    }

    public function create(Request $request)
    {

        $agendamentos = Agendamento::with('paciente')
            ->whereIn('status', ['AGENDADO', 'CONFIRMADO'])
            ->orderBy('data', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        $agendamento = $request->filled('agendamento_id')
            ? Agendamento::with('paciente')->findOrFail($request->agendamento_id)
            : null;

        return view('dentista.remarcacao.formulario', compact('agendamento', 'agendamentos'));
    }

    public function store(Request $request)
    {
        // 🔹 Pega o valor DIRETO do request, sem depender só da validação
        $agendamentoId = $request->input('agendamento_id');

        $dados = $request->validate([
            'agendamento_id'   => 'required|exists:agendamentos,id',
            'data_anterior'     => 'required|date',
            'data_nova'         => 'required|date|after:data_anterior',
            'hora_inicio_nova'  => 'required|date_format:H:i',
            'hora_fim_nova'     => 'required|date_format:H:i|after:hora_inicio_nova',
            'motivo'            => 'nullable|string|max:255',
        ]);

        $dados['user_id'] = auth()->id();

        DB::beginTransaction();
        try {
            $agendamentoOriginal = Agendamento::findOrFail($agendamentoId);

            $horaAnterior = $agendamentoOriginal->hora_inicio instanceof \Carbon\CarbonInterface
                ? $agendamentoOriginal->hora_inicio->format('H:i')
                : substr((string)$agendamentoOriginal->hora_inicio, 0, 5);

            // 🔹 Usa a variável garantida, não o array validado
            Remarcacao::create([
                'agendamento_id' => $agendamentoId, // ✅ VALOR GARANTIDO AQUI
                'data_anterior'   => $dados['data_anterior'] . ' ' . $horaAnterior,
                'data_nova'       => $dados['data_nova'] . ' ' . $dados['hora_inicio_nova'],
                'motivo'          => $dados['motivo'],
                'user_id'         => $dados['user_id'],
            ]);

            $agendamentoOriginal->update([
                'data'         => $dados['data_nova'],
                'hora_inicio'  => $dados['hora_inicio_nova'],
                'hora_fim'     => $dados['hora_fim_nova'],
                'status'       => 'REMARCADO',
            ]);

            DB::commit();
            return redirect()->route('remarcacao.index')
                ->with('sucesso', 'Agendamento remarcado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['erro' => 'Erro: ' . $e->getMessage()]);
        }
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
