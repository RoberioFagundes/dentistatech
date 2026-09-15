@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container mt-4 mb-5">

    {{-- Alertas unificados e padronizados --}}
    @if(session('success') || session('sucesso'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') ?? session('sucesso') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    @if(session('erro') || session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>{{ session('erro') ?? session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <div class="p-3 pb-0">
                <h1 class="fs-3 fw-bold text-dark mb-1">Fila de Atendimento</h1>
                <p class="text-muted small mb-3">Gerenciamento de pacientes aguardando atendimento hoje.</p>
            </div>

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th class="text-center">Posição na Fila</th>
                        <th>Data Agendada</th>
                        <th>Horário de Entrada</th>
                        <th class="text-center" width="200">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fila as $item)
                    <tr>
                        <td>
                            <span class="fw-semibold text-dark">
                                {{ $item->agendamento->paciente->nome ?? 'Nome não disponível' }}
                                {{ $item->agendamento->paciente->sobrenome ?? '' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                {{ $loop->iteration + ($fila->currentPage() - 1) * $fila->perPage() }}º
                            </span>
                        </td>

                        <td>
                            <i class="bi bi-calendar me-1 text-muted"></i>
                            {{ $item->agendamento?->data ? \Carbon\Carbon::parse($item->agendamento->data)->format('d/m/Y') : 'Não agendado' }}
                        </td>

                        <td>
                            <i class="bi bi-clock me-1 text-muted"></i>
                            <span class="badge bg-light text-dark border">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('atendimento.create', [
                                    'paciente_id' => $item->agendamento->paciente_id ?? null,
                                    'agendamento_id' => $item->agendamento_id
                                ]) }}" class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                    <i class="bi bi-check-lg"></i> Atender
                                </a>

                                <form action="{{ route('filaAtendimento.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja remover este paciente da fila?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remover da fila">
                                        <i class="bi bi-tr3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 mb-2 d-block text-secondary"></i>
                            <h5 class="fw-semibold mb-1">Fila vazia</h5>
                            <p class="mb-0">Não há pacientes aguardando atendimento no momento.</p>
                        </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $fila->links() }}
    </div>

</div>
@endsection