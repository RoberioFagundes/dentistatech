@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Histórico de Remarcações</h2>
        <a href="{{ route('remarcacao.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-calendar-x me-2"></i>Nova Remarcação
        </a>
    </div>

    {{-- Busca --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('remarcacao.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="busca" class="form-control" 
                           placeholder="Buscar por paciente ou data..."
                           value="{{ request('busca') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    @if(request('busca'))
                        <a href="{{ route('remarcacao.index') }}" class="btn btn-secondary w-100">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Paciente</th>
                            <th>Data Anterior</th>
                            <th>Nova Data</th>
                            <th>Motivo</th>
                            <th>Quem Remarcou</th>
                            <th width="120" class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remarcacoes as $remarc)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $remarc->agendamento->paciente->nome ?? '—' }}</div>
                                <small class="text-muted">Agendamento #{{ $remarc->agendamento_id }}</small>
                            </td>
                            <td class="text-muted small">
                                {{ $remarc->data_anterior->format('d/m/Y H:i') }}
                            </td>
                            <td class="fw-semibold text-primary">
                                {{ $remarc->data_nova->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-truncate" style="max-width:180px;">
                                {{ $remarc->motivo ?? 'Não informado' }}
                            </td>
                            <td class="small">
                                {{ $remarc->usuario->name ?? '—' }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('remarcacao.show', $remarc->id) }}" class="btn btn-sm btn-outline-info">Ver</a>
                                <form action="{{ route('remarcacao.destroy', $remarc->id) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir registro?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        @if($remarcacoes->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Nenhuma remarcação registrada ainda.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $remarcacoes->links() }}
    </div>
</div>
@endsection