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
        // Validação dos dados
        $dados = $request->validate([
            'agendamento_id'   => 'required|exists:agendamentos,id',
            'data_anterior'    => 'required|date',
            'data_nova'        => 'required|date|after:data_anterior',
            'hora_inicio_nova' => 'required',
            'hora_fim_nova'    => 'required|after:hora_inicio_nova',
            'motivo'           => 'nullable|string|max:255',
        ], [
            'data_nova.after'       => 'A nova data deve ser posterior à data original.',
            'hora_fim_nova.after'   => 'O horário de fim deve ser depois do horário de início.',
            'agendamento_id.exists' => 'O agendamento selecionado não existe no sistema.',
        ]);

        // Combina data e hora para salvar no campo datetime
        $dados['data_anterior'] = $dados['data_anterior'] . ' ' . $request->hora_inicio_nova;
        $dados['data_nova']     = $dados['data_nova'] . ' ' . $request->hora_inicio_nova;
        $dados['user_id']       = auth()->id(); // Usuário logado que fez a remarcação

        // Inicia transação para garantir integridade
        DB::beginTransaction();
        try {
            // Salva o registro de remarcação
            $remarcacao = Remarcacao::create($dados);

            // (Opcional) Atualiza o status do agendamento original se precisar
            $agendamento = Agendamento::findOrFail($request->agendamento_id);
            // $agendamento->status = 'remarcado'; // se tiver campo status
            // $agendamento->save();

            DB::commit();

            return redirect()->route('remarcacao.index')
                ->with('sucesso', 'Remarcação realizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('erro', 'Erro ao salvar: ' . $e->getMessage());
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
