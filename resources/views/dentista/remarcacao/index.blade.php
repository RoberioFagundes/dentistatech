@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Histórico de Remarcações</h4>
            <p class="text-muted mb-0">Registro de alterações de data e horário dos agendamentos.</p>
        </div>
        <a href="{{ route('remarcacao.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-x me-2"></i>Nova Remarcação
        </a>
    </div>

    {{-- Área de Busca --}}
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('remarcacao.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="busca" class="form-control" 
                           placeholder="Buscar por paciente, data ou motivo..."
                           value="{{ request('busca') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    @if(request()->filled('busca'))
                        <a href="{{ route('remarcacao.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de Remarcações --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="ps-4">Paciente</th>
                            <th>Data Anterior</th>
                            <th>Nova Data</th>
                            <th>Motivo</th>
                            <th>Responsável</th>
                            <th class="text-end pe-4" width="140">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($remarcacoes as $remarc)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">
                                    {{ $remarc->agendamento->paciente->nome ?? 'Não informado' }}
                                    {{ $remarc->agendamento->paciente->sobrenome ?? '' }}
                                </div>
                                <small class="text-muted">Agendamento #{{ $remarc->agendamento_id }}</small>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-clock-history me-1"></i>
                                {{ $remarc->data_anterior?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="fw-semibold text-primary">
                                <i class="bi bi-calendar-check me-1"></i>
                                {{ $remarc->data_nova?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="text-truncate" style="max-width: 180px;" title="{{ $remarc->motivo }}">
                                {{ $remarc->motivo ?? 'Não informado' }}
                            </td>
                            <td class="small">
                                {{ $remarc->usuario->name ?? 'Não registrado' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('remarcacao.show', $remarc->id) }}" class="btn btn-outline-info" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('remarcacao.destroy', $remarc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este registro permanentemente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir registro">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                <h5 class="fw-semibold mb-1">Nenhuma remarcação registrada</h5>
                                <p class="mb-0">Não há alterações de agendamento cadastradas no momento.</p>
                            </td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $remarcacoes->appends(request()->query())->links() }}
    </div>
</div>
@endsection