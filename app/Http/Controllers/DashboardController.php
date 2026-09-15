<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\OrcamentoPaciente;
use App\Models\Paciente;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Google\Service\Datastore\Count;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipo = strtolower(trim(auth()->user()->tipo));

        // Redirecionamento por perfil
        return match ($tipo) {
            'recepcionista' => view('recepcionista.dashboardRecepcionista'),
            'paciente'      => view('paciente.dashboardPaciente'),
            'dentista', 'administrador' => $this->dashboardDentistaAdmin(),
            default => abort(403, 'Perfil não autorizado'),
        };
    }

    /**
     * Lógica exclusiva para Dentista e Administrador
     */
    private function dashboardDentistaAdmin()
    {
        $userId = auth()->id();
        $hoje = today();
        $mesAtual = now()->month;

        // 1. Agendamentos de HOJE (apenas do dentista logado)
        $agendamentos = Agendamento::with(['paciente', 'dentista'])
            ->whereHas('dentista', fn($q) => $q->where('user_id', $userId))
            ->whereDate('data', $hoje)
            ->paginate(10, ['*'], 'agendamentos_page');

        // 2. Últimos Atendimentos (apenas do dentista logado)
        $atendimentos = Atendimento::with(['paciente', 'agendamento'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(5, ['*'], 'atendimentos_page');

        // 3. Cards do topo — TODOS filtrados pelo dentista logado
        $agendamentosHoje = Agendamento::whereHas('dentista', fn($q) => $q->where('user_id', $userId))
            ->whereDate('data', $hoje)
            ->count();

        $atendimentosHoje = Atendimento::where('user_id', $userId)
            ->whereDate('created_at', $hoje)
            ->count();

        // ✅ Conta apenas pacientes que tem agendamento ou atendimento com o dentista logado
        $totalPacientes = Paciente::whereHas('agendamentos.dentista', fn($q) => $q->where('user_id', $userId))
            ->orWhereHas('atendimentos', fn($q) => $q->where('user_id', $userId))
            ->distinct()
            ->count();

        $orcamentosAbertos = OrcamentoPaciente::where('user_id', $userId)
            ->where('saldo_restante', '>', 0)
            ->count();

        $recebimentosMes = Pagamento::where('user_id', $userId)
            ->whereMonth('created_at', $mesAtual)
            ->sum('valor_pago') ?? 0;

        // 4. Resumo do Mês (apenas atendimentos do dentista logado)
        $atendimentosMes = Atendimento::where('user_id', $userId)
            ->whereMonth('created_at', $mesAtual)
            ->get();

        $totalAtendimentosMes = $atendimentosMes->count();
        $realizados = $atendimentosMes->where('status', 'ATENDIDO')->count();
        $cancelados = $atendimentosMes->where('status', 'CANCELADO')->count();
        $remarcados = $atendimentosMes->where('status', 'REMARCADO')->count();
        $naoCompareceram = $atendimentosMes->where('status', 'NAO_COMPARECEU')->count();

        // Cálculo de porcentagens
        $calcularPerc = fn($valor) => $totalAtendimentosMes > 0 ? round(($valor / $totalAtendimentosMes) * 100) : 0;

        return view('dentista.dashboardDentista', [
            'agendamentos'         => $agendamentos,
            'atendimentos'         => $atendimentos,
            'agendamentosHoje'     => $agendamentosHoje,
            'atendimentosHoje'     => $atendimentosHoje,
            'totalPacientes'       => $totalPacientes,
            'orcamentosAbertos'    => $orcamentosAbertos,
            'recebimentosMes'      => $recebimentosMes,
            'totalAtendimentosMes' => $totalAtendimentosMes,
            'realizados'           => $realizados,
            'cancelados'           => $cancelados,
            'remarcados'           => $remarcados,
            'naoCompareceram'      => $naoCompareceram,
            'percRealizados'       => $calcularPerc($realizados),
            'percCancelados'       => $calcularPerc($cancelados),
            'percRemarcados'       => $calcularPerc($remarcados),
            'percNaoCompareceram'  => $calcularPerc($naoCompareceram),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */

    public function atendimentohoje()
    {
        $userId = auth()->id();

        $agendamentos = \App\Models\Agendamento::with(['paciente', 'dentista'])
            ->whereHas('dentista', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereDate('data', today())
            ->paginate(10);

        // Buscando os atendimentos do dentista logado para exibir no painel
        $atendimentos = \App\Models\Atendimento::with(['paciente', 'agendamento'])
            ->whereHas('dentista', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(5);

        return view('dentista.dashboardDentista', compact('agendamentos', 'atendimentos'));
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
