@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container-fluid my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Histórico de Atendimentos</h4>
            <p class="text-muted mb-0">Listagem de prontuários e intervenções clínicas.</p>
        </div>
        <a href="{{ route('atendimento.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle fs-5"></i>
            <span>Novo Atendimento</span>
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fs-7 fw-bold text-muted">
                        <tr>
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th style="width: 130px;">Data</th>
                            <th>Paciente</th>
                            <th>Dentista</th>
                            <th>Queixa Principal</th>
                            <th>Diagnóstico</th>
                            <th class="text-end pe-4" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($atendimentos) && count($atendimentos) > 0)
                            @foreach($atendimentos as $atendimento)
                                <tr>
                                    <td class="ps-4 fw-medium text-secondary">#{{ $atendimento->id }}</td>
                                    <td>
                                        @if($atendimento->created_at)
                                            <span class="fw-medium text-dark">{{ $atendimento->created_at->format('d/m/Y') }}</span>
                                            <small class="d-block text-muted">{{ $atendimento->created_at->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $atendimento->paciente->nome ?? 'Não informado' }}</div>
                                        @if($atendimento->agendamento_id)
                                            <small class="text-primary"><i class="bi bi-calendar-check me-1"></i>Agendado</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted fs-7 fw-medium">{{ $atendimento->dentista->nome ?? 'Não informado' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark d-inline-block text-truncate" style="max-width: 180px;" title="{{ $atendimento->queixa_principal }}">
                                            {{ $atendimento->queixa_principal ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted d-inline-block text-truncate" style="max-width: 200px;" title="{{ $atendimento->diagnostico }}">
                                            {{ $atendimento->diagnostico ?? 'Sem diagnóstico' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('atendimento.edit', $atendimento->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('atendimento.destroy', $atendimento->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este atendimento permanentemente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary"></i>
                                    Nenhum atendimento registrado ou dados não enviados pelo Controller.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($atendimentos) && method_exists($atendimentos, 'hasPages') && $atendimentos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $atendimentos->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection