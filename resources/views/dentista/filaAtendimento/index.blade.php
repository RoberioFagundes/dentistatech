@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container mt-4 mb-5">
    

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            {{-- Altere o título e a descrição --}}
<h1 class="fs-3 fw-bold text-dark mb-1">Fila de Atendimento</h1>
<p class="text-muted small mb-0">Gerenciamento de pacientes aguardando atendimento hoje.</p>

{{-- Na tabela, ajuste as colunas --}}
 <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th class="text-center">Posição na Fila</th>
                                <th>Data Agendada</th>
                                <th>Comparecimento</th>
                                <th width="200" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fila as $filas)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $filas->nome }}</span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                            {{ $filas->numero }}º
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <i class="fa fa-calendar text-muted me-1"></i>
                                        {{ $filas->agendamento ? \Carbon\Carbon::parse($filas->agendamento->data_hora)->format('d/m/Y') : 'Não agendado' }}
                                    </td>
                                    
                                    <td>
                                        <i class="fa fa-clock text-muted me-1"></i>
                                        <span class="badge bg-light text-dark border">
                                            {{ \Carbon\Carbon::parse($filas->created_at)->format('H:i:s') }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="#" method="POST" class="ajax-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success d-flex align-items-center btn-acao">
                                                    <i class="fa fa-check me-1"></i> Confirmar
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('filaAtendimento.destroy', $filas->id) }}" method="POST" class="ajax-form"
                                                  data-confirm="Tem certeza que deseja remover esta pessoa da fila?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-acao" title="Remover da fila">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa fa-info-circle fs-4 mb-2 d-block text-secondary"></i>
                                        Não há ninguém aguardando na fila no momento.
                                    </td>
                                </tr>
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