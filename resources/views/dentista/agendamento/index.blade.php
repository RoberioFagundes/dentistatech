@extends('dentista.layout.paginaDentista')

@section('pagina')

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="container-fluid mt-0 px-4 py-4">
    {{-- Área de Filtros --}}
    <form action="{{ route('agendamento.index') }}" method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-md-4">
            <input type="text" name="nome" class="form-control" placeholder="Buscar por nome do paciente..." value="{{ $nomeFiltro ?? '' }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="data" class="form-control" value="{{ $dataFiltro ?? '' }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('agendamento.index') }}" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4 gap-3">
        <div>
            <h2 class="h3 mb-1 text-dark fw-bold">Controle de Agendamentos</h2>
            <p class="text-muted small mb-0">Gerencie horários, pacientes e status dos atendimentos no consultório.</p>
        </div>
        <a href="{{ route('agendamento.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i> Novo Agendamento
        </a>
    </div>

    {{-- Tabela de Agendamentos --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th scope="col" class="px-4 py-3">Data / Hora</th>
                        <th scope="col" class="px-3 py-3">Paciente</th>
                        <th scope="col" class="px-3 py-3">Dentista</th>
                        <th scope="col" class="px-3 py-3">Procedimento</th>
                        <th scope="col" class="px-3 py-3">Status</th>
                        <th scope="col" class="px-3 py-3">Observações</th>
                        <th scope="col" class="px-4 py-3 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendamentos as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}
                            </div>
                            <div class="text-muted">
                                {{ \Carbon\Carbon::parse($item->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->hora_fim)->format('H:i') }}
                            </div>
                        </td>

                        <td class="px-3 py-3 text-dark fw-medium">
                            {{ $item->paciente->nome ?? 'Não informado' }}
                        </td>

                        <td class="px-3 py-3 text-secondary">
                            Dr(a). {{ $item->dentista->nome ?? 'Não informado' }}
                        </td>

                        <td class="px-3 py-3 text-muted">
                            {{ $item->procedimento->nome ?? '-' }}
                        </td>

                        <td class="px-3 py-3">
                            @switch($item->status)
                                @case('CONFIRMADO')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Confirmado</span>
                                    @break
                                @case('EM_ATENDIMENTO')
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill">Em Atendimento</span>
                                    @break
                                @case('CANCELADO')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Cancelado</span>
                                    @break
                                @default
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill">Agendado</span>
                            @endswitch
                        </td>

                        <td class="px-3 py-3 text-muted text-truncate" style="max-width: 180px;" title="{{ $item->observacao }}">
                            {{ $item->observacao ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-1">
                                {{-- Remarcar --}}
                                <a href="{{ route('remarcacao.create', ['agendamento_id' => $item->id, 'paciente_id' => $item->paciente_id]) }}" 
                                   class="btn btn-sm btn-outline-primary border-0 p-1.5" title="Remarcar">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>

                                {{-- Adicionar à Fila --}}
                                <form action="{{ route('fila.adicionar') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="agendamento_id" value="{{ $item->id }}">
                                    <input type="hidden" name="paciente_id" value="{{ $item->paciente_id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary border-0 p-1.5" title="Adicionar à Fila">
                                        <i class="bi bi-people"></i>
                                    </button>
                                </form>

                                {{-- Editar --}}
                                <a href="{{ route('agendamento.edit', $item->id) }}" class="btn btn-sm btn-outline-primary border-0 p-1.5" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- Excluir --}}
                                <form action="{{ route('agendamento.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1.5" title="Cancelar">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center text-secondary">
                            <div class="py-4">
                                <i class="bi bi-calendar-x display-5 text-muted mb-2 d-block"></i>
                                <h5 class="fw-semibold mb-1">Nenhum agendamento encontrado</h5>
                                <p class="mb-0 text-muted">Não há agendamentos correspondentes aos filtros aplicados.</p>
                            </div>
                        </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Estilos Complementares --}}
<style>
    .fw-bold { font-weight: 700 !important; }
    .fw-medium { font-weight: 500 !important; }
    .border-bottom { border-bottom: 1px solid #dee2e6 !important; }
    
    .bg-success-subtle { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .bg-primary-subtle { background-color: #cfe2ff !important; color: #084298 !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; color: #664d03 !important; }
    .bg-danger-subtle { background-color: #f8d7da !important; color: #842029 !important; }
</style>

@endsection

{{-- Script (mantive, mas ajustei para compatibilidade) --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Se precisar da função JS, mantenha-a; senão pode remover
        async function adicionarNaFila(agendamentoId, pacienteId) {
            try {
                const response = await fetch('/fila-atendimento/adicionar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ agendamento_id: agendamentoId, paciente_id: pacienteId })
                });

                if (response.ok) {
                    const data = await response.json();
                    alert('Sucesso: ' + data.message);
                    location.reload();
                } else {
                    const data = await response.json().catch(() => ({}));
                    alert('Erro: ' + (data.error || 'Falha na operação'));
                }
            } catch (error) {
                console.error(error);
                alert('Erro de conexão');
            }
        }
    });
</script>